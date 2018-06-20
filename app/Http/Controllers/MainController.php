<?php

namespace App\Http\Controllers;

use App\Eloquents\Entry;
use App\Eloquents\EntryDetail;
use App\Eloquents\Feed;
use App\Services\SimpleXML;
use Storage;

class MainController extends Controller
{
    /**
     * Index page.
    **/
    public function index() : \Illuminate\View\View
    {
        $type = request()->query('type') ?: null;
        $kind = request()->query('kind') ?: null;
        $observatoryName = request()->query('observatory') ?: null;

        $appends = [];

        if ($kind) {
            $selected = 'Kind: '.$kind;
            $appends['kind'] = $kind;
            $entry_ids = EntryDetail::select('entry_id')->where('kind_of_info', $kind)->groupBy('entry_id')->get();

            $simple_entry_ids = [];
            foreach ($entry_ids as $entry_id) {
                $simple_entry_ids[] = $entry_id->entry_id;
            }
            $entries = Entry::whereIn('id', $simple_entry_ids)->orderBy('updated', 'desc');
        } elseif ($type) {
            $selected = 'Type: '.trans('feedtypes.'.$type);
            $appends['type'] =$type;
            $entries = Feed::whereType($type)->first()->entries()->orderBy('updated', 'desc');
        } else {
            $selected = 'Select Type or Kind';
            $entries = Entry::orderBy('updated', 'desc');
        }

        $kindList = EntryDetail::select('kind_of_info', 'entry_id')
                    ->selectRaw('count(*) as count')
                    ->groupBy('kind_of_info');

        if ($observatoryName) {
            $appends['observatory'] = $observatoryName;
            $entries = $entries->whereObservatoryName($observatoryName);
            $kindList = $kindList->whereHas('entry', function ($query) use ($observatoryName) {
                            return $query->whereObservatoryName($observatoryName);
                        });
        }

        $entries = $entries
                    ->paginate(15)
                    ->appends($appends);

        $feeds = Feed::get(['uuid', 'url'])
                    ->sortByFeedType()
                    ->map(function ($feed) use ($observatoryName) {
                        if ($observatoryName) {
                            $feed->count = $feed->entries()->whereObservatoryName($observatoryName)->count();
                        } else {
                            $feed->count = $feed->entries()->count();
                        }
                        return $feed;
                    })->filter(function ($feed) {
                        return $feed->count > 0;
                    });

        $kindList = $kindList->get()->sortByKind();

        return view('index', [
                   'entries' => $entries,
                   'feeds' => $feeds,
                   'selected' => $selected,
                   'observatory' => $observatoryName,
                   'kindList' => $kindList,
                   'queries' => collect(request()->query()),
               ]);
    }

    /**
     * Entry page.
    **/
    public function entry(EntryDetail $entry) : \Illuminate\View\View
    {
        $doc = Storage::get('entry/'.$entry->uuid);
        $feed = $entry->entry->feed;
        $entryArray = collect((new SimpleXML($doc, true))->toArray(true, true));

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
    **/
    public function entryXml($uuid) : \Illuminate\Http\Response
    {
        $doc = Storage::get('entry/'.$uuid);
        return response($doc, 200)
                    ->header('Content-Type', 'application/xml');
    }

    /**
     * Entry json.
    **/
    public function entryJson($uuid) : \Illuminate\Http\JsonResponse
    {
        $doc = Storage::get('entry/'.$uuid);
        return response()->json((new SimpleXML($doc, true))->toArray(true, true),
                                200, [], JSON_UNESCAPED_UNICODE);
    }
}
