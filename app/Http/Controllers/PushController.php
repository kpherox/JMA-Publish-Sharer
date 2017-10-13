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
            Log::notice('hub_mode not subscribe|unsubscribe');
            return response('Not Found', 404)->header('Content-Type', 'text/plain');
        }

        if (env('IS_HUB_VERIFY_TOKEN', false)) {
            $hubVerifyToken = $request->hub_verify_token;
            if (empty($hubVerifyToken)) {
                Log::notice('Not exist hub_verify_token');
                return response('Not exist hub_verify_token', 403)->header('Content-Type', 'text/plain');
            }
            if ($hubVerifyToken != env('HUB_VERIFY_TOKEN')) {
                Log::notice('Incorrect hub_verify_token');
                return response('Incorrect hub_verify_token', 403)->header('Content-Type', 'text/plain');
            }
        }
        $hubChallenge = $request->hub_challenge;
        Log::notice($hubMode);
        Log::info('Success subscribe check');

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
            $signature = explode('=',$request->header('x-hub-signature'));
            if (empty($signature)) {
                Log::notice('Not exist hub signature');
                return response('Not exist X-Hub-Signature', 403)->header('Content-Type', 'text/plain');
            }
            $hash = hash_hmac($signature[0],$content,env('HUB_VERIFY_TOKEN'));
            if ($signature[1] != $hash) {
                Log::notice('Invalid hub signature');
                return response('Invalid X-Hub-Signature', 403)->header('Content-Type', 'text/plain');
            }

            Log::debug('Success check hub signature');
        }
        if (false === ($feed = simplexml_load_string($content))) {
            $message = "Feed Parse ERROR";
            Log::error($message.": ".$content);
            return $message;
        }
        Log::debug('Success feed parse');
        
        $client = new Client();
        // Fetch JMA xml
        foreach ($feed->entry as $entry) {
            $title = (string)$entry->title;
            $url = (string)$entry->link['href'];
            try {
                $response = $client->get($url);
            } catch (ClientException $e) {
                report($e);
                continue;
            }
        }
    }
}

