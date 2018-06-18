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
        $kind = (request()->has('kind') && collect(config('jmaxmlkinds'))->has(request()->query('kind'))) ? request()->query('kind') : null;

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
        } else {
            $entries = Entry::orderBy('updated', 'desc');
            $entries = $entries
                ->paginate(15);
            $paginateLinks = $entries->links();
        }

        $kindOrder = collect(config('jmaxmlkinds'))->keys();

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
        return view(config('jmaxmlkinds.'.$entryArray['Control']['Title'].'.view', 'entry'), [
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
