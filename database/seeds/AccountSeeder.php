<?php

use App\Eloquents;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $providers = ['twitter', 'line', 'github'];

        $users = factory(Eloquents\User::class, 3)
            ->create()
            ->each(function ($user) use ($providers) {
                foreach ($providers as $provider) {
                    $account = factory(Eloquents\LinkedSocialAccount::class)
                        ->make(['provider_name' => $provider]);
                    $user->accounts()->save($account);
                }
            });

        $user = $users->first();
        $user->accounts->each(function ($account) {
            if ($account->provider_name !== 'twitter' && $account->provider_name != 'line') {
                return;
            }

            $isAllow = $account->provider_name === 'twitter' ? true : false;

            $setting = factory(Eloquents\AccountSetting::class)
                ->make(['type' => 'notification', 'settings' => [
                    'isAllow' => $account->provider_name === 'twitter' ? true : false,
                    'filters' => [
                        'feedtypes' => [
                            'extra' => true,
                        ],
                    ],
                ]]);
            $account->settings()->save($setting);
        });
    }
}
