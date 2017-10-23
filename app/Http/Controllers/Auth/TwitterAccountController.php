<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;

class TwitterAccountController extends SocialAccountController
{
    protected $provider = 'twitter';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function getProvider() {
        return $this->provider;
    }

    /**
     * Redirect the user to the Twitter authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider() {
        return \Socialite::driver($this->getProvider())->redirect();
    }
}
