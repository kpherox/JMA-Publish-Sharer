<?php

namespace App\Services;

use App\Eloquents\User;
use App\Eloquents\AccountSetting;
use App\Eloquents\LinkedSocialAccount;
use App\Notifications\TestNotify;
use Illuminate\Support\Collection;
use Laravel\Socialite\Contracts\User as ProviderUser;

class SocialAccountsService
{
    /**
     * @param  \Laravel\Socialite\Contracts\User $providerUser
     * @param  string $provider
     */
    public function findOrCreate(ProviderUser $providerUser, string $provider) : User
    {
        $account = LinkedSocialAccount::firstOrNew(['provider_name' => $provider, 'provider_id' => $providerUser->getId()]);

        $didExistAccount = $account->id;

        if (! $didExistAccount && auth()->guest() && User::where('email', $providerUser->getEmail())->exists()) {
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
     * @param  $providerId
     */
    public function getAccountSettings(string $provider, $providerId) : Collection
    {
        $accounts = auth()->user()
            ->accounts()
            ->whereProviderName($provider)
            ->whereProviderId($providerId);
        if (! $accounts->exists()) {
            throw new \Exception('Not found account.');
        }

        $allSettings = collect([]);

        $settings = $accounts->first()->settings()->get();
        $settings->each(function ($setting) use (&$allSettings) {
            $allSettings->put($setting->type, $setting->settings);
        });

        $settingTypes = collect(['notification']);
        $settingTypes->each(function ($type) use (&$allSettings) {
            if (! $allSettings->has($type)) {
                $allSettings->put($type, []);
            }
        });

        return $allSettings;
    }

    /**
     * @param  string $provider
     * @param  $providerId
     * @param  string $settingType
     * @param  string $settingKey
     * @param  $settingValue
     */
    public function updateAccountSettings(string $provider, $providerId, string $settingType, string $settingKey, $settingValue) : Collection
    {
        $settingTypes = collect(['notification']);
        if (! $settingTypes->contains($settingType)) {
            throw new \Exception('Invalid setting type.');
        }

        $accounts = auth()->user()
            ->accounts()
            ->whereProviderName($provider)
            ->whereProviderId($providerId);
        if (! $accounts->exists()) {
            throw new \Exception('Not found account.');
        }

        $settings = $accounts->first()->settings()->whereType($settingType);
        $setting = $settings->firstOrCreate(['type' => $settingType]);
        $setting->forceFill([
            'settings->'.$settingKey => $settingValue
        ])->save();

        $settings->save($setting);

        return $setting->settings;
    }
    /**
     * @param  string $provider
     * @param  $providerId
     */
    public function deleteLinkedAccount(string $provider, $providerId) : string
    {
        $accounts = auth()->user()
            ->accounts()
            ->whereProviderName($provider)
            ->whereProviderId($providerId);
        if ($accounts->exists()) {
            $accounts->each(function ($account) {
                $account->settings()->delete();
            });
            $accounts->delete();
        } else {
            throw new \Exception('Not found account.');
        }

        return 'Success unlinked!';
    }

    /**
     * @param  string $provider
     * @param  $providerId
     * @param  string $message
     */
    public function testNotify(string $provider, $providerId, string $message) : string
    {
        $accounts = auth()->user()
            ->accounts()
            ->whereProviderName($provider)
            ->whereProviderId($providerId);
        if ($accounts->exists()) {
            $accounts->first()->notify(new TestNotify($message));
        } else {
            throw new \Exception('Not found account.');
        }

        return 'Successfully notified!';
    }

    /**
     * Set ProviderUser property to LinkedSocialAccount columns.
     *
     * @param  \App\Eloquents\LinkedSocialAccount inout $account
     * @param  \Laravel\Socialite\Contracts\User $user
     * @param  bool $isOAuthOne
     * @return void
     */
    private function setAccountColumn(LinkedSocialAccount &$account, ProviderUser $user, bool $isOAuthOne)
    {
        $account->name = $user->getName();
        $account->nickname = $user->getNickname();
        $account->avatar = $this->originalSizeImageUrl($user->getAvatar());
        $account->token = $user->token;
        $account->token_secret = $isOAuthOne ? $user->tokenSecret : $user->refreshToken;
    }

    /**
     * Return original size image's url for Twitter.
     *
     * @param  string $url
     */
    private function originalSizeImageUrl(string $url) : string
    {
        return preg_replace("/https?:\/\/(.+?)_normal.(jpg|jpeg|png|gif)/", 'https://$1.$2', $url);
    }

    /**
     * Contains provider to oauth1.
     *
     * @param  string $provider
     */
    private function isOAuthOne(string $provider) : bool
    {
        return config('services.'.$provider.'.oauth1', false);
    }
}
