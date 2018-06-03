<?php

namespace App\Http\Controllers;

use App\Eloquents\Entry;

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
}
