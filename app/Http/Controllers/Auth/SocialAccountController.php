<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Services\SocialAccountsService;

abstract class SocialAccountController extends Controller
{
    /**
     * Provider name.
     *
     * @var String
    **/
    private $provider = '';

    /**
     * Getter for provider name.
    **/
    protected function getProvider() : String
    {
        return $this->provider;
    }

    /**
     * Setter for provider name.
    **/
    protected function setProvider(String $value) : String
    {
        return $this->provider = $value;
    }

    /**
     * Redirect the user to the authentication page.
     */
    abstract public function redirectToProvider() : RedirectResponse;

    /**
     * Link social account to User.
     */
    public function linkToUser() : RedirectResponse
    {
        return $this->redirectToProvider();
    }

    /**
     * Unlink account from User.
     *
     * @return RedirectResponse|JsonResponse
     */
    public function unlinkFromUser(SocialAccountsService $accountService, Request $request)
    {
        try {
            $message = $accountService->deleteLinkedAccount($this->getProvider(), (Int)$request->id);
        } catch (\Exception $e) {
            abort_if(!$request->ajax(), 403, 'Can\'t delete');

            return new JsonResponse([
                    'status' => 'Can\'t delete',
                    'statusCode' => 403,
                    'message' => $e->getMessage(),
                ], 403);
        }

        if ($request->ajax()) {
            return new JsonResponse([
                'status' => 'OK',
                'statusCode' => 201,
                'message' => $message,
            ], 201);
        }

        return redirect()->route('home.accounts');
    }

    /**
     * Obtain the user information
     */
    public function handleProviderCallback(SocialAccountsService $accountService) : RedirectResponse
    {
        $redirectTo = auth()->check() ? 'home.accounts' : 'login';

        try {
            $user = \Socialite::with($this->getProvider())->user();
        } catch (\Exception $e) {
            return redirect()->route($redirectTo);
        }

        try {
            $authUser = $accountService->findOrCreate($user, $this->getProvider());
        } catch (\Exception $e) {
            \Log::error($e);

            return redirect()->route($redirectTo);
        }

        if (auth()->guest()) {
            auth()->login($authUser, true);

            return redirect()->route('home.index');
        }

        return redirect()->route('home.accounts');
    }
}
