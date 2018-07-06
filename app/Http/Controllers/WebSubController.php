<?php

namespace App\Http\Controllers;

use App\Services\SimpleXML;
use Illuminate\Http\Response;
use App\Services\WebSubHandler;

class WebSubController extends Controller
{
    /**
     * Subscribe Check JMA.
     */
    public function subscribeCheck() : Response
    {
        // Subscribe check
        try {
            WebSubHandler::verifyToken(request('hub_mode'), request('hub_verify_token'));
            \Log::info('Success subscribe check');
        } catch (\Exception $e) {
            \Log::info('Failed subscribe check');
            abort(403, $e->getMessage());
        }

        return response(request('hub_challenge'), 200)->header('Content-Type', 'text/plain');
    }

    /**
     * Recive JMA Publish
     */
    public function receiveFeed() : Response
    {
        // Xml parse
        $content = request()->getContent();

        try {
            WebSubHandler::verifySignature($content, request()->header('x-hub-signature'));
            \Log::debug('Success check hub signature');
        } catch (\Exception $e) {
            abort(403, $e->getMessage());
        }

        $simpleXml = new SimpleXML($content);

        try {
            $feed = $simpleXml->toArray(true);
        } catch (\Exception $e) {
            \Log::warning($e);
            abort(403, $e);
        }
        \Log::debug('Success feed parse');

        WebSubHandler::saveFeedAndEntries($feed);

        return response(201);
    }
}
