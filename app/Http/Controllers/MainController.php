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
        $type = (request()->has('type') && collect(config('jmaxml.feedtypes'))->contains(request()->query('type'))) ? request()->query('type') : null;
        $kind = (request()->has('kind') && collect(config('jmaxml.kinds'))->has(request()->query('kind'))) ? request()->query('kind') : null;

        $feeds = Feed::all()->sortByFeedType();

        if ($kind) {
            $entry_ids = EntryDetail::select('entry_id')->where('kind_of_info', $kind)->groupBy('entry_id');
            $entry_ids = $entry_ids
                ->paginate(15)
                ->appends(['kind' => $kind]);
            $paginateLinks = $entry_ids->links();
            $simple_entry_ids = [];
            foreach ($entry_ids as $entry_id) {
                $simple_entry_ids[] = $entry_id->entry_id;
            }
            $entries = Entry::find($simple_entry_ids);
            $selected = 'Kind: '.$kind;
        } elseif ($type) {
            $entries = $feeds
                ->filter(function ($feed) use ($type) {
                    return $feed->type === $type;
                })
                ->first()->entries()
                ->paginate(15)
                ->appends(['type' => $type]);
            $paginateLinks = $entries->links();
            $selected = 'Type: '.trans('feedtypes.'.$type);
        } else {
            $entries = Entry::orderBy('updated', 'desc');
            $entries = $entries
                ->paginate(15);
            $paginateLinks = $entries->links();
            $selected = 'Select Type or Kind';
        }

        $kindOrder = collect(config('jmaxml.kinds'))->keys();

        $kindList = EntryDetail::select('kind_of_info')
                        ->selectRaw('count(*) as kind_count')
                        ->groupBy('kind_of_info')
                        ->get()
                        ->sortByKind()
                        ->map(function ($entry) {
                            return [
                                'count' => $entry->kind_count,
                                'kind' => $entry->kind_of_info
                            ];
                        });

        return view('index', [
                   'entries' => $entries,
                   'feeds' => $feeds,
                   'selected' => $selected,
                   'kindList' => $kindList,
                   'paginateLinks' => $paginateLinks,
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
