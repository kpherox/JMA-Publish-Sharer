<?php

namespace App\Http\Controllers;

use \Datetime;
use Log;
use Illuminate\Http\Request;
use App\Eloquents\Feed;
use App\Eloquents\Entry;
//use GuzzleHttp\Client;
//use GuzzleHttp\Exception\ClientException;

class PushController extends Controller
{
    /**
     * Subscribe Check JMA
     *
     * @return \Illuminate\Http\Response
     */
    function subscribeCheck(Request $request) {
        // Subscribe check
        $hubMode = $request->hub_mode;
        if ($hubMode != 'subscribe' && $hubMode != 'unsubscribe') abort(404, 'Not Found');

        if (env('IS_HUB_VERIFY_TOKEN', false)) {
            $hubVerifyToken = $request->hub_verify_token;
            if (empty($hubVerifyToken)) abort(403, 'Not exist hub_verify_token');
            if ($hubVerifyToken != env('HUB_VERIFY_TOKEN')) abort(403, 'Incorrect hub_verify_token');
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
            if (empty($signature[1])) abort(403, 'Not exist hub signature');
            $hash = hash_hmac($signature[0],$content,env('HUB_VERIFY_TOKEN'));
            if ($signature[1] != $hash) abort(403, 'Invalid hub signature');
            Log::debug('Success check hub signature');
        }

        if (false === ($feed = simplexml_load_string($content))) {
            $message = "Feed Parse ERROR";
            Log::error($message.": ".$content);
            return $message;
        }
        Log::debug('Success feed parse');

        $uuid = explode(':', (string)$feed->id);
        $feeds = Feed::firstOrNew(['uuid' => $uuid[2]]);

        $dateTime = new DateTime((string)$feed->updated);
        $feeds->updated = $dateTime->format("Y-m-d H:i:s");
        foreach ($feed->link as $link) {
            if ($link['rel'] != 'self') continue;
            $feeds->url = (string)$link['href'];
        }

        $feeds->save();

        // Fetch JMA xml
        foreach ($feed->entry as $entry) {
            $entryUUID = explode(':', (string)$entry->id);
            array_push($entriesUUID, $entryUUID);
            $entries = Entry::firstOrNew(['uuid' => $entryUUID[2]]);

            $entries->kind_of_info = (string)$entry->title;
            $entries->feed_uuid = $uuid;
            $entries->observatory_name = (string)$entry->author->name;
            $entries->headline = (string)$entry->content;
            $entries->url = (string)$entry->link['href'];
            $dateTime = new DateTime((string)$entry->updated);
            $entries->updated = $dateTime->format("Y-m-d H:i:s");

            $entries->save();

            /*
            try {
                $client = new Client();
                $response = $client->get($url);
            } catch (ClientException $e) {
                report($e);
                continue;
            }
             */
        }
    }
}

