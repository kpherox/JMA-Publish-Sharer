<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\RedirectResponse;

class GitHubAccountController extends SocialAccountController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (method_exists('Base', '__construct')) {
            parent::__construct();
        }
        $this->setProvider('github');
    }

    /**
     * Redirect the user to the GitHub authentication page.
     */
    public function redirectToProvider() : RedirectResponse
    {
        return \Socialite::driver($this->getProvider())->redirect();
    }
}
