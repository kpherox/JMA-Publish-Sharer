<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Page status.
     *
     * @var array
     */
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
        $providerName = collect(config('services.providers'));

        $socialAccounts = $providerName->map(function($item, $key) use ($user) {
            return $user->accounts->where('provider_name', $key)->map(function($account) {
                return collect($account)->forget(['id', 'user_id', 'updated_at']);
            });
        });

        $endpoints = $providerName->map(function($item, $key) {
            return collect([
                'unlink' => route($key.'.unlink'),
                'notify' => route($key.'.notify')
            ]);
        });

        return view($accounts, [
            'menus' => $this->menus,
            'socialAccounts' => $socialAccounts,
            'providerName' => $providerName,
            'endpoints' => $endpoints,
            'existsEmail' => $user->existsEmailAndPassword() ? 1 : 0
        ]);
    }
}
