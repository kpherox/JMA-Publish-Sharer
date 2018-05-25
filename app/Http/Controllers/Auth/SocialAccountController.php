<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Services\SocialAccountsService;

abstract class SocialAccountController extends Controller
{
    private $isLogin = true;

    protected function isLogin() : Bool
    {
        return $this->isLogin;
    }

    protected function enableLogin() : Bool
    {
        return $this->isLogin = true;
    }

    protected function disableLogin() : Bool
    {
        return $this->isLogin = false;
    }

    private $provider = '';

    protected function getProvider() : String
    {
        return $this->provider;
    }

    protected function setProvider(String $value) : String
    {
        return $this->provider = $value;
    }

    /**
     * Redirect the user to the authentication page.
     */
    abstract public function redirectToProvider() : RedirectResponse;

    /**
     * Link social account for User account.
     */
    abstract public function linkToUser() : RedirectResponse;

    /**
     * Obtain the user information
     */
    public function handleProviderCallback(SocialAccountsService $accountService) : RedirectResponse
    {
        try {
            $user = \Socialite::with($this->getProvider())->user();
        } catch (\Exception $e) {
            return redirect('/login');
        }

        try {
            $authUser = $accountService->findOrCreate(
                $user, $this->getProvider()
            );
        } catch (\Exception $e) {
            return redirect('/home');
        }

        auth()->login($authUser, true);

        return redirect()->to('/home');
    }
}
