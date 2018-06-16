<?php

namespace App\Http\Controllers;

use App\Eloquents\Entry;
use App\Eloquents\EntryDetail;
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
        $sortBaseKinds = function($a, $b) use($kindOrder) {
            return ($kindOrder->search($a) > $kindOrder->search($b));
        };

        $kindList = EntryDetail::select('kind_of_info')
                        ->selectRaw('count(*) as kind_count')
                        ->groupBy('kind_of_info')
                        ->get()
                        ->map(function ($entry) {
                            return [
                                'count' => $entry->kind_count,
                                'kind' => $entry->kind_of_info
                            ];
                        })->sort(function($a, $b) use($sortBaseKinds) {
                            return $sortBaseKinds($a['kind'], $b['kind']);
                        });

        return view('index', [
                   'entries' => $entries,
                   'kindList' => $kindList,
                   'paginateLinks' => $paginateLinks,
               ]);
    }

    /**
     * Entry page.
    **/
    public function entry($entry) : \Illuminate\View\View
    {
        $doc = Storage::get('entry/'.$entry);
        $entryArray = collect((new SimpleXML($doc, true))->toArray(true, true));
        return view(config('jmaxmlkinds.'.$entryArray['Control']['Title'].'.view', 'entry'), [
                    'entry' => $entryArray,
                    'entryUuid' => $entry,
                ]);
    }

    /**
     * Entry xml.
    **/
    public function entryXml($entry) : \Illuminate\Http\Response
    {
        $doc = Storage::get('entry/'.$entry);
        return response($doc, 200)
                    ->header('Content-Type', 'application/xml');
    }

    /**
     * Entry json.
    **/
    public function entryJson($entry) : \Illuminate\Http\JsonResponse
    {
        $doc = Storage::get('entry/'.$entry);
        return response()->json((new SimpleXML($doc, true))->toArray(true, true),
                                200, [], JSON_UNESCAPED_UNICODE);
    }
}
