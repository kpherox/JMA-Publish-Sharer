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
        $kind = (request()->has('kind') && collect(config('jmaxmlkinds'))->has(request()->query('kind'))) ? request()->query('kind') : null;

        $entries = Entry::select('observatory_name', 'headline', 'updated')
                        ->groupConcat('uuid', null, true)
                        ->groupConcat('kind_of_info', null, true)
                        ->orderBy('updated', 'desc')
                        ->groupBy('observatory_name', 'headline', 'updated');

        if ($kind) {
            $entries = $entries
                ->havingRaw('(kind_of_info LIKE \''.$kind.'\' OR kind_of_info LIKE \'%,'.$kind.'\' OR kind_of_info LIKE \''.$kind.',%\' OR kind_of_info LIKE \'%,'.$kind.',%\')')
                ->simplePaginate(15)
                ->appends(['kind' => $kind]);
        } else {
            $entries = $entries->simplePaginate(15);
        }


        $paginateLinks = $entries->links();

        $kindOrder = collect(config('jmaxmlkinds'))->keys();
        $sortBaseKinds = function($a, $b) use($kindOrder) {
            return ($kindOrder->search($a) > $kindOrder->search($b));
        };

        $entries = $entries->map(function($entry) use($sortBaseKinds) {
            preg_match('/【(.*)】(.*)/', $entry->headline, $headline);

            $entry->headline = collect([
                'title' => $headline[1],
                'headline' => $headline[2],
            ]);

            $kind = collect(explode(',', $entry->kind_of_info));
            $kindWithUuid = collect(explode(',', $entry->uuid))
                    ->combine($kind)
                    ->sort($sortBaseKinds);

            $entry->uuid = $kindWithUuid->keys();
            $entry->kind_of_info = $kindWithUuid->values();

            return $entry;
        });

        $kindList = Entry::select('kind_of_info')
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
    public function entry(Entry $entry) : \Illuminate\View\View
    {
        $entryArray = collect((new SimpleXML($entry->xml_document, true))->toArray(true, true));
        return view(config('jmaxmlkinds.'.$entryArray['Control']['Title'].'.view', 'entry'), [
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
