<?php

namespace App\Http\Controllers;

use Log;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class PushController extends Controller
{
    /**
     * Subscribe Check JMA
     *
     * @return \Illuminate\Http\Response
     */
    function subscriber(Request $request) {
        // Subscribe check
        $hubMode = $request->hub_mode;
        if ($hubMode != 'subscribe' && $hubMode != 'unsubscribe') {
            Log::error('hub_mode not subscribe|unsubscribe');
            return response('Not Found', 404)->header('Content-Type', 'text/plain');
        }

        if (env('IS_HUB_VERIFY_TOKEN', false)) {
            $hubVerifyToken = $request->hub_verify_token;
            if ($hubVerifyToken != env('HUB_VERIFY_TOKEN')) {
                Log::error('Incorrect hub_verify_token');
                return response('Unknown Request', 404)->header('Content-Type', 'text/plain');
            }
        }
        $hubChallenge = $request->hub_challenge;
        Log::debug($hubMode);
        Log::debug('Success subscribe check')

        return response($hubChallenge, 200)->header('Content-Type', 'text/plain');
    }

    /**
     * Recive JMA Publish
     *
     * @return \Illuminate\Http\Response
     */
    function receiveFeed(Request $request) {
        // Xml parse
        $content = $request->getContent();

        if (env('IS_HUB_VERIFY_TOKEN', false)) {
            if (!isset($request->header('X-Hub-Signature'))) {
                return response('Invalid X-Hub-Signature', 404)->header('Content-Type', 'text/plain');
            }
            $signature = $request->header('X-Hub-Signature');
            $hash = hash_hmac("sha1",$content,env('HUB_VERIFY_TOKEN');
            if ($signature != $hash) {
                return response('Invalid X-Hub-Signature', 404)->header('Content-Type', 'text/plain');
            }

        }
        if (false === ($feed = simplexml_load_string($content))) {
            $message = "feed Parse ERROR";
            Log::error($message);
            return $message;
        }
        Log::debug('success feed parse');
        
        $client = new Client();
        // Fetch JMA xml
        foreach ($feed->entry as $entry) {
            Log::debug($entry->title);
            $url = (string)$entry->link['href'];
            Log::debug($url);
            try {
                $response = $client->get($url);
            } catch (ClientException $e) {
                Log::error($e->getMessage());
                continue;
            }
        }
    }
}
