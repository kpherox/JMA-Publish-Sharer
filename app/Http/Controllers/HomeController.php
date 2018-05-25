<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    private $pageList = [
        'index' => [
            'name' => 'Top Page',
            'isThis' => false,
        ],
        'home.index' => [
            'name' => 'Dashboard',
            'isThis' => false,
        ],
        'home.accounts' => [
            'name' => 'Social Accounts',
            'isThis' => false,
        ],
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $index = 'home.index';
        $this->pageList[$index]['isThis'] = true;
        return view($index, ['pageList' => $this->pageList]);
    }

    /**
     * Show the application linked accounts.
     *
     * @return \Illuminate\Http\Response
     */
    public function accounts()
    {
        $accounts = 'home.accounts';
        $this->pageList[$accounts]['isThis'] = true;
        $socialAccounts = auth()->user()->accounts();
        return view($accounts, [
            'pageList' => $this->pageList,
            'twitterAccounts' => $socialAccounts->where('provider_name', 'twitter')
        ]);
    }
}
