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
        $providerName = collect(config('services.providers'));
        $socialAccounts = $providerName->map(function($item, $key) use ($user) {
            return $user->accounts->where('provider_name', $key)->map(function($account) {
                return collect($account)->forget(['created_at', 'updated_at']);
            });
        });
        $isSafeUnlink = $user->existsEmailAndPassword() || $socialAccounts->count() > 1;

        return view($accounts, [
            'menus' => $this->menus,
            'socialAccounts' => $socialAccounts,
            'providerName' => $providerName,
            'endpoints' => collect([
                'unlink' => url('/')
            ]),
            'isSafeUnlink' => $isSafeUnlink ? 1 : 0
        ]);
    }
}
