<?php

namespace App\Http\Controllers;

use App\Eloquents\Entry;
use App\Eloquents\EntryDetail;
use App\Services\SimpleXML;

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
        $entry = EntryDetail::select('xml_document', 'uuid')->where('uuid', $entry)->first();
        $entryArray = collect((new SimpleXML($entry->xml_document, true))->toArray(true, true));
        return view(config('jmaxmlkinds.'.$entryArray['Control']['Title'].'.view', 'entry'), [
                    'entry' => $entryArray,
                    'entryUuid' => $entry->uuid,
                ]);
    }

    /**
     * Entry xml.
    **/
    public function entryXml($entry) : \Illuminate\Http\Response
    {
        $entry = EntryDetail::select('xml_document')->where('uuid', $entry)->first();
        return response($entry->xml_document, 200)
                    ->header('Content-Type', 'application/xml');
    }

    /**
     * Entry json.
    **/
    public function entryJson($entry) : \Illuminate\Http\JsonResponse
    {
        $entry = EntryDetail::select('xml_document')->where('uuid', $entry)->first();
        return response()->json((new SimpleXML($entry->xml_document, true))->toArray(true, true),
                                200, [], JSON_UNESCAPED_UNICODE);
    }
}
