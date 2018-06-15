<?php

namespace App\Services;

class WebSubHandler
{
    /**
     * Verify feed's signature.
    **/
    public static function verifySignature(String $requestBody, String $hubSignature = null) : Bool
    {
        if (!config('app.isUseWebSubVerifyToken')) {
            return true;
        }

        if(empty($hubSignature)) {
            throw new \Exception('Not exist x-hub-signature header');
        }

        $signature = collect(explode('=',$hubSignature));
        if($signature->count() !== 2) {
            throw new \Exception('Invalid hubSignature');
        }

        $hash = hash_hmac($signature->first(), $requestBody, config('app.websubVerifyToken'));
        if($signature->last() !== $hash) {
            throw new \Exception('Invalid signature');
        }

        return true;
    }

    /**
     * Verify token for subscribe check.
    **/
    public static function verifyToken(String $hubMode = null, String $hubVerifyToken = null) : Bool
    {
        if ($hubMode !== 'subscribe' && $hubMode !== 'unsubscribe') {
            throw new \Exception('Not exist hub.mode');
        }
        \Log::notice($hubMode);

        if (!config('app.isUseWebSubVerifyToken')) {
            return true;
        }

        if (empty($hubVerifyToken)) {
            throw new \Exception('Not exist hub.verify_token');
        }

        if ($hubVerifyToken !== config('app.websubVerifyToken')) {
            throw new \Exception('Incorrect hub.verify_token');
        }

        return true;
    }
}
