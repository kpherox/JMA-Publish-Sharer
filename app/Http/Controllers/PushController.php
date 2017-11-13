<?php

namespace App\Http\Controllers;

use Log;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Eloquents\Feed;
use App\Eloquents\Entry;

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
        abort_if($hubMode != 'subscribe' && $hubMode != 'unsubscribe', 404, 'Not Found');

        if (env('IS_HUB_VERIFY_TOKEN', false)) {
            $hubVerifyToken = $request->hub_verify_token;
            abort_if(is_null($hubVerifyToken), 403, 'Not exist hub_verify_token');
            abort_if($hubVerifyToken != env('HUB_VERIFY_TOKEN'), 403, 'Incorrect hub_verify_token');
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
            abort_if(is_null($signature[1]), 403, 'Not exist hub signature');
            $hash = hash_hmac($signature[0],$content,env('HUB_VERIFY_TOKEN'));
            abort_if($signature[1] != $hash, 403, 'Invalid hub signature');
            Log::debug('Success check hub signature');
        }

        if (false === ($feed = simplexml_load_string($content))) {
            $message = "Feed Parse ERROR";
            Log::error($message.": ".$content);
            return $message;
        }
        Log::debug('Success feed parse');

        $feedUuid = explode(':', (string)$feed->id);
        $carbon = Carbon::parse((string)$feed->updated);
        $feeds = Feed::firstOrNew(['uuid' => $feedUuid[2]]);
        $feeds->updated = $carbon;
        foreach ($feed->link as $link) {
            if ($link['rel'] == 'self') {
                $feeds->url = (string)$link['href'];
                break;
            }
        }
        $feeds->save();

        // Fetch JMA xml
        $entryArray = [];
        foreach ($feed->entry as $entry) {
            $entryUuid = explode(':', (string)$entry->id);
            $carbon = Carbon::parse((string)$entry->updated);

            $entryArray[] = [
                'uuid' => $entryUuid[2],
                'kind_of_info' => (string)$entry->title,
                'feed_uuid' => $feedUuid[2],
                'observatory_name' => (string)$entry->author->name,
                'headline' => (string)$entry->content,
                'url' => (string)$entry->link['href'],
                'updated' => $carbon,
            ];
        }

        $entries = Entry::insertOnDuplicateKey($entryArray, [
            'kind_of_info', 'feed_uuid', 'observatory_name', 'headline', 'updated',
        ]);
    }
}

