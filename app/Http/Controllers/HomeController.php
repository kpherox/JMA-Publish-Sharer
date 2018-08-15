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
        'topPage' => [
            'name' => 'Top Page',
        ],
        'index' => [
            'name' => 'Dashboard',
        ],
        'accounts' => [
            'name' => 'Social Accounts',
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
        data_set($this->menus, 'index.isCurrent', true);

        return view('home.index', ['menus' => $this->menus]);
    }

    /**
     * Show the application linked accounts.
     */
    public function accounts() : View
    {
        data_set($this->menus, 'accounts.isCurrent', true);
        $providers = collect(config('services'))->filter(function ($provider) {
            return data_get($provider, 'socialite', false);
        });

        $providersName = $providers->map(function ($provider) {
            return $provider['name'];
        });

        $socialAccounts = $providers->map(function ($item, $key) use ($providers) {
            return auth()->user()->accounts()->select('provider_name', 'provider_id', 'name', 'nickname', 'avatar')->where('provider_name', $key)->get()->map(function ($account) use ($providers) {
                $account->can_notify = data_get($providers, $account->provider_name.'.notification', false);

                return $account;
            });
        });

        $endpoints = $providers->map(function ($item, $key) {
            return collect([
                'settings' => route($key.'.settings'),
                'unlink' => route($key.'.unlink'),
                'notify' => route($key.'.notify'),
            ]);
        });

        $feedtypes = collect(config('jmaxml.feedtypes'));
        $feedtypeFilter = $feedtypes->map(function ($feedtype) {
            return [
                $feedtype => [
                    'name' => trans('feedtypes.'.$feedtype),
                    'isAllow' => false,
                ],
            ];
        })->collapse();

        $notificationFilters = collect([
            'feedtypes' => [
                'name' => trans('feedtypes.name'),
                'items' => $feedtypeFilter->all(),
            ],
        ]);

        return view('home.accounts', [
            'menus' => $this->menus,
            'socialAccounts' => $socialAccounts,
            'providersName' => $providersName,
            'endpoints' => $endpoints,
            'existsEmail' => auth()->user()->existsEmailAndPassword(),
            'notificationFilters' => $notificationFilters,
        ]);
    }
}
