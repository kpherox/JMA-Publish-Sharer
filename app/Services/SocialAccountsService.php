<?php

namespace App\Services;

use Laravel\Socialite\Contracts\User as ProviderUser;
use Auth;
use App\Eloquents\User;
use App\Eloquents\LinkedSocialAccount;

class SocialAccountsService
{
    public function findOrCreate(ProviderUser $providerUser, $provider)
    {
        $account = LinkedSocialAccount::where('provider_name', $provider)
                   ->where('provider_id', $providerUser->getId())
                   ->first();

        if ($account && Auth::guest()) {
            $account->provider_token = $providerUser->token;
            $account->provider_token_secret = $providerUser->tokenSecret;
            $account->save();
            return $account->user;
        } elseif ($account) {
            throw new \Exception('It is already linked to other account');
        }

        $user = Auth::guest() ? User::where('email', $providerUser->getEmail())->first() : Auth::user();

        if (! $user && Auth::guest()) {
            $user = User::create([  
                'email' => $providerUser->getEmail(),
                'name'  => $providerUser->getName(),
            ]);
        }

        $user->accounts()->create([
            'provider_name' => $provider,
            'provider_id' => $providerUser->getId(),
            'provider_token' => $providerUser->token,
            'provider_token_secret' => $providerUser->tokenSecret,
        ]);

        return $user;
    }
}
