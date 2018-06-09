<?php

namespace App\Http\Controllers;

use App\Eloquents\Entry;
use App\Services\SimpleXML;

class MainController extends Controller
{
    /**
     * Index page.
    **/
    public function index() : \Illuminate\View\View
    {
        $entries = Entry::select('observatory_name', 'headline', 'updated')
                        ->groupBy('observatory_name', 'headline', 'updated')
                        ->groupConcat('uuid', null, true)
                        ->groupConcat('kind_of_info', null, true)
                        ->groupConcat('url', null, true)
                        ->orderBy('updated', 'desc')
                        ->simplePaginate(5);

        $paginateLinks = $entries->links();

        $entries = $entries->map(function($entry) {
            preg_match('/【(.*)】(.*)/', $entry->headline, $headline);

            $entry->headline = collect([
                'title' => $headline[1],
                'headline' => $headline[2],
            ]);

            $entry->uuid = collect(explode(',', $entry->uuid));
            $entry->kind_of_info = collect(explode(',', $entry->kind_of_info));
            $entry->url = collect(explode(',', $entry->url));

            return $entry;
        });

        return view('index', [
                   'entries' => $entries,
                   'paginateLinks' => $paginateLinks,
               ]);
    }

    /**
     * Entry page.
    **/
    public function entry(Entry $entry) : \Illuminate\View\View
    {
        $entryArray = collect((new SimpleXML($entry->xml_document, true))->toArray(true, true));
        return view(config('jmaxmlkinds.view.'.$entryArray['Control']['Title'], 'entry'), [
                    'entry' => $entryArray,
                    'entryUuid' => $entry->uuid,
                ]);
    }

    /**
     * Entry xml.
    **/
    public function entryXml(Entry $entry) : \Illuminate\Http\Response
    {
        return response($entry->xml_document, 200)
                    ->header('Content-Type', 'application/xml');
    }

    /**
     * Entry json.
    **/
    public function entryJson(Entry $entry) : \Illuminate\Http\JsonResponse
    {
        return response()->json((new SimpleXML($entry->xml_document, true))->toArray(true, true),
                                200, [], JSON_UNESCAPED_UNICODE);
    }
}
