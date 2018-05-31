<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

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
     */
    public function index() : View
    {
        $index = 'home.index';
        $this->menus[$index]['isCurrent'] = true;
        return view($index, ['menus' => $this->menus]);
    }

    /**
     * Show the application linked accounts.
     */
    public function accounts() : View
    {
        $accounts = 'home.accounts';
        $user = auth()->user();

        $this->menus[$accounts]['isCurrent'] = true;
        $socialAccounts = $user->accounts;
        $isSafeUnlink = $user->existsEmailAndPassword() || $socialAccounts->count() > 1;

        return view($accounts, [
            'menus' => $this->menus,
            'socialAccounts' => collect([
                'Twitter' => $socialAccounts->where('provider_name', 'twitter'),
                'GitHub' => $socialAccounts->where('provider_name', 'github'),
            ]),
            'endpoints' => collect([
                'unlink' => url('/')
            ]),
            'isSafeUnlink' => $isSafeUnlink ? 1 : 0
        ]);
    }
}
