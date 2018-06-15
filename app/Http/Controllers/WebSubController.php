<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\WebSubHandler;

class WebSubController extends Controller
{
    /**
     * Subscribe Check JMA
     */
    public function subscribeCheck(Request $request) : Response
    {
        // Subscribe check
        try {
            WebSubHandler::verifyToken($request->hub_mode, $request->hub_verify_token);
            \Log::info('Success subscribe check');
        } catch (\Exception $e) {
            \Log::info('Failed subscribe check');
            abort(403, $e->getMessage());
        }

        return response($request->hub_challenge, 200)->header('Content-Type', 'text/plain');
    }

    /**
     * Recive JMA Publish
     *
     * @return void
     */
    public function receiveFeed(Request $request)
    {
        // Xml parse
        $content = $request->getContent();

        try {
            WebSubHandler::verifySignature($content, $request->header('x-hub-signature'));
            \Log::debug('Success check hub signature');
        } catch (\Exception $e) {
            abort(403, $e->getMessage());
        }

        libxml_use_internal_errors(true);
        $feedXml = simplexml_load_string($content);

        if (!$feedXml) {
            $message = 'Feed parse error';
            \Log::warning($message);

            foreach(libxml_get_errors() as $error) {
                \Log::warning(trim($error->message));
            }

            libxml_clear_errors();
            abort(403, $message);
        }
        \Log::debug('Success feed parse');

        $feed = json_decode(json_encode($feedXml), true);

        WebSubHandler::saveFeedAndEntries($feed);
    }
}

