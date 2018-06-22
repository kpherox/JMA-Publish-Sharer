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

        $data = $this->entries($type, $kind)->merge(['queries' => collect(request()->query())]);

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

        $observatories = Entry::select('observatory_name')
                    ->selectRaw('count(*) as count')
                    ->selectRaw('MAX(updated) as max_updated')
                    ->orderBy('max_updated', 'desc')
                    ->groupBy('observatory_name')
                    ->get();

        $data = $this->entries($type, $kind, $observatoryName)->merge([
                    'observatory' => $observatoryName,
                    'observatories' => $observatories,
                    'queries' => collect(request()->query())->put('ovservatory', $observatoryName),
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
                            return $query->whereType($type);
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
            $entries = $entries->whereObservatoryName($observatoryName);
            $feeds = $feeds->whereHas('entries', function ($query) use ($observatoryName) {
                            return $query->whereObservatoryName($observatoryName);
                        })->withCount(['entries' => function ($query) use ($observatoryName) {
                            return $query->whereObservatoryName($observatoryName);
                        }]);
            $kindList = $kindList->whereHas('entry', function ($query) use ($observatoryName) {
                            return $query->whereObservatoryName($observatoryName);
                        });
        } else {
            $feeds = $feeds->withCount('entries');
        }

        $entries = $entries->paginate(15)->appends($appends);

        $feeds = $feeds->get()->sortByFeedType();
        $kindList = $kindList->get()->sortByKind();

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
        $doc = \Storage::get('entry/'.$entry->uuid);
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
    public function entryXml(string $uuid) : Response
    {
        $doc = \Storage::get('entry/'.$uuid);
        return response($doc, 200)
                    ->header('Content-Type', 'application/xml');
    }

    /**
     * Entry json.
     *
     * @param  string $uuid
    **/
    public function entryJson(string $uuid) : JsonResponse
    {
        $doc = \Storage::get('entry/'.$uuid);
        return response()->json((new SimpleXML($doc, true))->toArray(true),
                                200, [], JSON_UNESCAPED_UNICODE);
    }
}
