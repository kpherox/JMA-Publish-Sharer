<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    private $menus = [
        'index' => [
            'name' => 'Top Page',
            'isCurrent' => false,
        ],
        'home.index' => [
            'name' => 'Dashboard',
            'isCurrent' => false,
        ],
        'home.accounts' => [
            'name' => 'Social Accounts',
            'isCurrent' => false,
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
        $this->menus[$index]['isCurrent'] = true;
        return view($index, ['menus' => $this->menus]);
    }

    /**
     * Show the application linked accounts.
     *
     * @return \Illuminate\Http\Response
     */
    public function accounts()
    {
        $accounts = 'home.accounts';
        $this->menus[$accounts]['isCurrent'] = true;
        $socialAccounts = auth()->user()->accounts;
        return view($accounts, [
            'menus' => $this->menus,
            'socialAccounts' => [
                'Twitter' => $socialAccounts->where('provider_name', 'twitter'),
                'GitHub' => $socialAccounts->where('provider_name', 'github'),
            ],
        ]);
    }
}
