<?php

namespace App\Services;

use Laravel\Socialite\Contracts\User as ProviderUser;
use App\Eloquents\User;
use App\Eloquents\LinkedSocialAccount;
use App\Notifications\TestNotify;

class SocialAccountsService
{
    /**
     * @param  \Laravel\Socialite\Contracts\User $providerUser
     * @param  string $provider
    **/
    public function findOrCreate(ProviderUser $providerUser, string $provider) : User
    {
        $account = LinkedSocialAccount::firstOrNew(['provider_name' => $provider, 'provider_id' => $providerUser->getId()]);

        $didExistAccount = $account->id;

        if (!$didExistAccount && auth()->guest() && User::where('email', $providerUser->getEmail())->exists()) {
            throw new \Exception('Already used this E-mail address.');
        }

        if ($didExistAccount && auth()->check() && $account->user_id !== auth()->id()) {
            throw new \Exception('It is already linked to other account.');
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

    /**
     * @param  string $provider
     * @param  int $providerId
    **/
    public function deleteLinkedAccount(string $provider, int $providerId) : string
    {
        $account = auth()->user()->accounts()->where([
            ['provider_name', $provider],
            ['provider_id', $providerId],
        ]);
        if ($account->exists()) {
            $account->delete();
        } else {
            throw new \Exception('Not found account.');
        }
        return 'Success unlinked!';
    }

    public function testNotify(string $provider, int $providerId, string $message) : string
    {
        $account = auth()->user()->accounts()->where([
            ['provider_name', $provider],
            ['provider_id', $providerId],
        ]);
        if ($account->exists()) {
            $account->first()->notify(new TestNotify($message));
        } else {
            throw new \Exception('Not found account.');
        }
        return 'Successfully notified!';
    }

    /**
     * Set ProviderUser property to LinkedSocialAccount columns
     *
     * @param  \App\Eloquents\LinkedSocialAccount inout $account
     * @param  \Laravel\Socialite\Contracts\User $user
     * @param  bool $isOAuthOne
     * @return void
    **/
    private function setAccountColumn(LinkedSocialAccount &$account, ProviderUser $user, bool $isOAuthOne)
    {
        $account->name = $user->getName();
        $account->nickname = $user->getNickname();
        $account->avatar = $this->originalSizeImageUrl($user->getAvatar());
        $account->token = $user->token;
        $account->token_secret = $isOAuthOne ? $user->tokenSecret : $user->refreshToken;
    }

    /**
     * Return original size image's url for Twitter
     *
     * @param  string $url
    **/
    private function originalSizeImageUrl(string $url) : string
    {
        return preg_replace("/https?:\/\/(.+?)_normal.(jpg|jpeg|png|gif)/", "https://$1.$2", $url);
    }

    /**
     * Contains provider to oauth1
     *
     * @param  string $provider
    **/
    private function isOAuthOne(string $provider) : bool
    {
        return collect(config('services.oauth1'))->contains($provider);
    }
}
