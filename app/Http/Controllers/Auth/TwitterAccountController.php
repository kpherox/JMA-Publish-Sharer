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
        if (auth()->check() && $this->isLogin()) {
            return redirect('/home');
        }

        return \Socialite::driver($this->getProvider())
            ->with([
                'force_login' => $this->forceLogin
            ])->redirect();
    }

    /**
     * Link Twitter account for User account.
     *
     * @return \Illuminate\Http\Response
     */
    public function linkToUser() : RedirectResponse
    {
        if (auth()->guest()) {
            return redirect('/login');
        }

        $this->disableLogin();
        $this->forceLogin = true;
        return $this->redirectToProvider();
    }
}
