<?php

namespace App\Http\Controllers;

use App\Eloquents\Feed;
use App\Eloquents\Entry;
use Illuminate\View\View;
use App\Services\SimpleXML;
use Illuminate\Http\Response;
use App\Eloquents\EntryDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class MainController extends Controller
{
    /**
     * Index page.
     */
    public function index() : View
    {
        $type = request('type', null);
        $kind = request('kind', null);

        $data = $this->entries($type, $kind)->merge([
                    'routeUrl' => route('index'),
                ]);

        return view('index', $data->all());
    }

    /**
     * Observatory page.
     *
     * @param  string $observatoryName
     */
    public function observatory(string $observatoryName) : View
    {
        $type = request('type', null);
        $kind = request('kind', null);

        $observatories = Entry::select('observatory_name as name')
                    ->selectRaw('count(*) as count')
                    ->selectRaw('MAX(updated) as max_updated')
                    ->orderBy('max_updated', 'desc')
                    ->groupBy('observatory_name')
                    ->get()
                    ->map(function ($observatory) {
                        $observatory->url = route('observatory', ['observatory' => $observatory->name]);

                        return $observatory;
                    });

        $data = $this->entries($type, $kind, $observatoryName)->merge([
                    'observatory' => $observatoryName,
                    'observatories' => $observatories,
                    'routeUrl' => route('observatory', ['observatory' => $observatoryName]),
                ]);

        return view('observatory', $data->all());
    }

    /**
     * Create entries list & filter list for index & observatory page.
     *
     * @param  string? $type
     * @param  string? $kind
     * @param  string? $observatoryName
     */
    private function entries(string $type = null, string $kind = null, string $observatoryName = null) : Collection
    {
        $typeOrKind = 'Select Type or Kind';
        $selected = '';
        $entries = Entry::orderBy('updated', 'desc');
        $appends = [];

        $feeds = Feed::select(['url'])
                    ->having('entries_count', '>=', 1);
        $kindList = EntryDetail::select('kind_of_info')
                    ->selectRaw('count(*) as count')
                    ->groupBy('kind_of_info');

        if ($observatoryName) {
            $entries = $entries->ofObservatory($observatoryName);
            $feeds = $feeds->withCount(['entries' => function ($query) use ($observatoryName) {
                return $query->ofObservatory($observatoryName);
            }]);
            $kindList = $kindList->whereHas('entry', function ($query) use ($observatoryName) {
                return $query->ofObservatory($observatoryName);
            });
        } else {
            $feeds = $feeds->withCount('entries');
        }

        $feeds = $feeds->get()->sortByType();
        $kindList = $kindList->get()->sortByKind();

        if ($type) {
            $selected = $type;
            $typeOrKind = 'Type: '.trans('feedtypes.'.$type);
            $appends['type'] = $type;
            $entries = $entries->whereHas('feed', function ($query) use ($type) {
                return $query->ofType($type);
            });
        } elseif ($kind) {
            $selected = $kindList->search(function ($i) use ($kind) {
                return $i->kind_of_info === $kind;
            });
            $typeOrKind = 'Kind: '.$kind;
            $appends['kind'] = $kind;
            $entries = $entries->whereHas('entryDetails', function ($query) use ($kind) {
                return $query->where('kind_of_info', $kind);
            });
        }

        $entries = $entries->paginate(15)->appends($appends);

        return collect([
            'entries' => $entries,
            'selected' => $selected,
            'typeOrKind' => $typeOrKind,
            'feeds' => $feeds,
            'kindList' => $kindList,
        ]);
    }

    /**
     * Entry page.
     *
     * @param  \App\Eloquents\EntryDetail $entry
     */
    public function entry(EntryDetail $entry) : View
    {
        $doc = $entry->xml_file;
        $feed = $entry->entry->feed;
        $entryArray = collect((new SimpleXML($doc, true))->toArray(true));

        $kindViewName = config('jmaxml.kinds.'.data_get($entryArray, 'Control.Title').'.view');
        $viewName = \View::exists($kindViewName) ? $kindViewName : 'entry';

        return view($viewName, [
                    'entry' => $entryArray,
                    'entryUuid' => $entry->uuid,
                    'feed' => $feed,
                ]);
    }

    /**
     * Entry xml.
     *
     * @param  string $uuid
     */
    public function entryXml(EntryDetail $entry) : Response
    {
        $headers = ['Content-Type' => 'application/xml'];
        if (collect(request()->header('Accept-Encoding'))->contains('gzip') && $entry->isGzippedXmlFile()) {
            $header['Content-Encoding'] = 'gzip';
            return response($entry->gzipped_xml_file, 200, $headers);
        }

        return response($entry->xml_file, 200, $headers);
    }

    /**
     * Entry json.
     *
     * @param  string $uuid
     */
    public function entryJson(EntryDetail $entry) : JsonResponse
    {
        $doc = $entry->xml_file;

        return response()->json((new SimpleXML($doc, true))->toArray(true),
                                200, [], JSON_UNESCAPED_UNICODE);
    }
}
