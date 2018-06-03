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
        $entries = Entry::select(['uuid', 'kind_of_info', 'observatory_name', 'headline', 'url', 'updated'])
                        ->groupBy('observatory_name', 'headline', 'updated')
                        ->orderBy('updated', 'desc')
                        ->simplePaginate(5);

        $paginateLinks = $entries->links();

        $entries = $entries->map(function($entry) {
            preg_match('/【(.*)】(.*)/', $entry->headline, $headline);

            $entry->headline = [
                'title' => $headline[1],
                'headline' => $headline[2],
            ];

            return $entry;
        });

        return view('index', [
                   'entries' => $entries,
                   'paginateLinks' => $paginateLinks,
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
