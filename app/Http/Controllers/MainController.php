<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Eloquents\Entry;

class MainController extends Controller
{
    /**
     * Index page
     *
     * @return Response
    **/
    public function index(Request $request)
    {
        return view('index', [
                    'entries' => Entry::select(['uuid', 'kind_of_info', 'observatory_name', 'headline', 'url', 'updated'])
                                     ->orderBy('updated', 'desc')
                                     ->simplePaginate(5),
                ]);
    }
}
