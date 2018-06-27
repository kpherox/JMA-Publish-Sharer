<?php

namespace App\Http\Controllers;

use App\Eloquents\Entry;
use App\Eloquents\EntryDetail;
use App\Eloquents\Feed;
use App\Services\SimpleXML;
use Illuminate\View\View;
use Illuminate\Support\Collection;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

class MainController extends Controller
{
    /**
     * Index page.
    **/
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
    **/
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
                    ->map(function($observatory) {
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
    **/
    private function entries(string $type = null, string $kind = null, string $observatoryName = null) : Collection
    {
        $selected = 'Select Type or Kind';
        $entries = Entry::orderBy('updated', 'desc');
        $appends = [];

        if ($type) {
            $selected = 'Type: '.trans('feedtypes.'.$type);
            $appends['type'] = $type;
            $entries = $entries->whereHas('feed', function ($query) use ($type) {
                            return $query->ofType($type);
                        });
        } elseif ($kind) {
            $selected = 'Kind: '.$kind;
            $appends['kind'] = $kind;
            $entries = $entries->whereHas('entryDetails', function ($query) use ($kind) {
                            return $query->where('kind_of_info', $kind);
                        });
        }

        $feeds = Feed::select(['uuid', 'url']);
        $kindList = EntryDetail::select('kind_of_info', 'entry_id')
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

        $entries = $entries->paginate(15)->appends($appends);

        $feeds = $feeds->having('entries_count', '>=', 1)->get()
                    ->sortByType()
                    ->map(function($feed) {
                        $feed->param = '?type='.$feed->type;
                        return $feed;
                    });

        $kindList = $kindList->get()
                    ->sortByKind()
                    ->map(function($kind) {
                        $kind->param = '?kind='.$kind->kind_of_info;
                        return $kind;
                    });

        return collect([
            'entries' => $entries,
            'feeds' => $feeds,
            'selected' => $selected,
            'kindList' => $kindList,
        ]);
    }

    /**
     * Entry page.
     *
     * @param  \App\Eloquents\EntryDetail $entry
    **/
    public function entry(EntryDetail $entry) : View
    {
        $doc = $entry->xml_file;
        $feed = $entry->entry->feed;
        $entryArray = collect((new SimpleXML($doc, true))->toArray(true));

        $kindViewName = config('jmaxml.kinds.'.$entryArray['Control']['Title'].'.view');
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
    **/
    public function entryXml(EntryDetail $entry) : Response
    {
        $doc = $entry->xml_file;
        return response($doc, 200)
                    ->header('Content-Type', 'application/xml');
    }

    /**
     * Entry json.
     *
     * @param  string $uuid
    **/
    public function entryJson(EntryDetail $entry) : JsonResponse
    {
        $doc = $entry->xml_file;
        return response()->json((new SimpleXML($doc, true))->toArray(true),
                                200, [], JSON_UNESCAPED_UNICODE);
    }
}
