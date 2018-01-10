<?php

namespace App\Services;

use Laravel\Socialite\Contracts\User as ProviderUser;
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
            if (auth()->guest()) {
                return $account->user;
            } else {
                throw new \Exception('It is already linked to other account');
            }
        }

        if (auth()->guest()) {
            $user = auth()->user;
        } elseif (! User::where('email', $providerUser->getEmail())->exists()) {
            $user = User::create([
                'email' => $providerUser->getEmail(),
                'name'  => $providerUser->getName(),
            ]);
        } else {
            throw new \Exception('Already used this E-mail address');
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
