<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use App\Services\SocialAccountsService;

class LineAccountController extends SocialAccountController
{
    private $forceLogin = false;

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
        $this->setProvider('line');
    }

    /**
     * Redirect the user to the LINE authentication page.
     */
    public function redirectToProvider() : RedirectResponse
    {
        return \Socialite::driver($this->getProvider())
                    ->redirect();
    }

    public function testNotify(SocialAccountsService $accountService)
    {
        $isAjax = request()->ajax();

        try {
            $message = $accountService->testNotify($this->getProvider(), request('id'), request('message'));
        } catch (\Exception $e) {
            report($e);
            if ($isAjax) {
                return new JsonResponse([
                    'status' => 'Can\'t notify',
                    'statusCode' => 403,
                    'message' => $e->getMessage(),
                ], 403);
            }

            abort(403, 'Can\'t notify');
        }

        if ($isAjax) {
            return new JsonResponse([
                'status' => 'OK',
                'statusCode' => 201,
                'message' => $message,
            ], 201);
        }

        return redirect()->route('home.accounts');
    }
}
