<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;

class TwitterAccountController extends SocialAccountController
{
    protected $provider = 'twitter';

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
