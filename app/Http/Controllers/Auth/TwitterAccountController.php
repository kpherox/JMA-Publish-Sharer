<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class TwitterAccountController extends SocialAccountController
{
    private $forceLogin = false;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (method_exists('Base', '__construct')) parent::__construct();
        $this->setProvider('twitter');
    }

    /**
     * Redirect the user to the Twitter authentication page.
     */
    public function redirectToProvider() : RedirectResponse
    {
        return \Socialite::driver($this->getProvider())
                    ->with(['force_login' => $this->forceLogin])
                    ->redirect();
    }

    /**
     * Link Twitter account for User account.
     */
    public function linkToUser() : RedirectResponse
    {
        $this->forceLogin = true;
        return parent::linkToUser();
    }
}
