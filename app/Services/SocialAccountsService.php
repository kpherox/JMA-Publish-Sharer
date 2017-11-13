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
        $accounts = LinkedSocialAccount::where('provider_name', $provider)
                   ->where('provider_id', $providerUser->getId());

        if ($accounts->exists()) {
            $account = $accounts->first();
            $account->account_name = $providerUser->nickname;
            $account->account_avatar = $providerUser->avatar;
            $account->account_token = $providerUser->token;
            $account->account_token_secret = $providerUser->tokenSecret;
            $account->save();
            if (Auth::guest()) {
                return $account->user;
            } else {
                throw new \Exception('It is already linked to other account');
            }
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
            'account_name' => $providerUser->nickname,
            'account_avatar' => $providerUser->avatar,
            'account_token' => $providerUser->token,
            'account_token_secret' => $providerUser->tokenSecret,
        ]);

        return $user;
    }
}
