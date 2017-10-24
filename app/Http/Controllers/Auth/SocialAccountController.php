<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\SocialAccountsService;

abstract class SocialAccountController extends Controller
{
    abstract protected function getProvider();

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('guest')->except('logout');
    }

    /**
     * Redirect the user to the authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    abstract public function redirectToProvider();

    /**
     * Obtain the user information
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback(SocialAccountsService $accountService)
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
