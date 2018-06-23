<?php

use Illuminate\Database\Seeder;
use App\Eloquents;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $providers = ['twitter', 'github'];

        $users = factory(Eloquents\User::class, 3)
            ->create()
            ->each(function ($user) use ($providers) {
                foreach ($providers as $provider) {
                    $user->accounts()->save(factory(Eloquents\LinkedSocialAccount::class)->make(['provider_name' => $provider]));
                }
            });
    }
}
