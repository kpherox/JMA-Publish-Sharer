<?php

namespace App\Services;

use Laravel\Socialite\Contracts\User as ProviderUser;
use App\Eloquents\User;
use App\Eloquents\LinkedSocialAccount;

class SocialAccountsService
{
    public function findOrCreate(ProviderUser $providerUser, String $provider) : User
    {
        $account = LinkedSocialAccount::firstOrNew(['provider_name' => $provider, 'provider_id' => $providerUser->getId()]);

        $didExistAccount = !($account->wasRecentlyCreated = !$account->id);

        if ($account->wasRecentlyCreated && auth()->guest() && User::where('email', $providerUser->getEmail())->exists()) {
            throw new \Exception('Already used this E-mail address');
        }

        if ($didExistAccount && auth()->check() && $account->user !== auth()->user()) {
            throw new \Exception('It is already linked to other account');
        }

        $this->setAccountColumn($account, $providerUser, $this->isOAuthOne($provider));

        if ($didExistAccount) {
            $account->save();
            return $account->user;
        }

        $user = auth()->user() ?? User::create(['email' => $providerUser->getEmail(), 'name' => $providerUser->getName()]);

        $user->accounts()->save($account);

        return $user;
    }

    private function setAccountColumn(LinkedSocialAccount &$account, ProviderUser $user, Bool $isOAuthOne)
    {
        $account->account_name = $user->getNickname();
        $account->account_avatar = $this->originalSizeImageUrl($user->getAvatar());
        $account->account_token = $user->token;
        $account->account_token_secret = $isOAuthOne ? $user->tokenSecret : $user->refreshToken;
    }

    private function originalSizeImageUrl(String $url) : String
    {
        return preg_replace("/https?:\/\/(.+?)_normal.(jpg|jpeg|png|gif)/", "https://$1.$2", $url);
    }

    private function isOAuthOne(String $provider) : Bool
    {
        return collect(config('services.oauth1'))->contains($provider);
    }
}
