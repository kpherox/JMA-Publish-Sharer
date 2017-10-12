<?php

namespace App\Http\Controllers;

use Log;
use Illuminate\Http\Request;

class PushController extends Controller
{
    /**
     * Subscribe Check JMA
     *
     * @return \Illuminate\Http\Response
     */
    function subscriber(Request $request) {
        // Subscribe check
        $hubMode = $request->input('hub_mode');
        $hubChallenge = $request->input('hub_challenge');

        if ($hubMode == 'subscribe' || $hubMode == 'unsubscribe') {
            return response($hubChallenge, 200)->header('Content-Type', 'text/plain');
        } else {
            Log::debug('hub_mode not subscribe|unsubscribe');
            return response('Not Found', 404)->header('Content-Type', 'text/plain');
        }
    }

    /**
     * Recive JMA Publish
     *
     * @return \Illuminate\Http\Response
     */
    function receiveFeed(Request $request) {
        // Xml parse
        $content = $request->getContent();
        if (false === ($feed = simplexml_load_string($content))) {
            $message = "feed Parse ERROR";
            Log::error($message);
            return $message;
        }
        Log::debug('success feed parse');

        $client = new GuzzleHttp\Client();
        // Fetch JMA xml
        foreach ($feed->entry as $entry) {
            $url = $entry->link['href'];
            Log::debug($url);
            $response = $client->get($url);
            Log::debug($res->getBody());
        }
    }
}
