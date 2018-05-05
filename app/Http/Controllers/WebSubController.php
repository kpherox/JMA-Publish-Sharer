<?php

namespace App\Http\Controllers;

use Log;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Eloquents\Feed;
use App\Eloquents\Entry;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;

class WebSubController extends Controller
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

        $feedUuid = explode(':', (string)$feed->id)[2];
        $feedUpdated = Carbon::parse((string)$feed->updated);
        $feedUpdated->setTimezone(config('app.timezone'));
        $feeds = Feed::firstOrNew(['uuid' => $feedUuid]);
        $feeds->updated = $feedUpdated;
        foreach ($feed->link as $link) {
            if ($link['rel'] == 'self') {
                $feeds->url = (string)$link['href'];
                break;
            }
        }
        $feeds->save();

        $client = new Client();
        // Fetch JMA xml
        $entryArrays = [];
        $promises = [];
        $results  = [];

        foreach ($feed->entry as $entry) {
            $entryUuid = explode(':', (string)$entry->id)[2];
            $kindOfInfo = (string)$entry->title;
            $observatoryName = (string)$entry->author->name;
            $headline = (string)$entry->content;
            $updated = Carbon::parse((string)$entry->updated);
            $updated->setTimezone(config('app.timezone'));
            $url = (string)$entry->link['href'];

            $promises[$entryUuid] = $client->getAsync($url);

            $entryArrays[$entryUuid] = [
                'kind_of_info' => $kindOfInfo,
                'feed_uuid' => $feedUuid,
                'observatory_name' => $observatoryName,
                'headline' => $headline,
                'url' => $url,
                'updated' => $updated,
            ];
        }

        foreach (Promise\settle($promises)->wait() as $key => $obj) {
            switch ($obj['state']) {
                case 'fulfilled':
                    $results[$key] = $obj['value'];
                    break;
                case 'rejected':
                    $results[$key] = new Response($obj['reason']->getCode());
                    break;
                default:
                    $results[$key] = new Response(0);
            }
        }

        $entryRecords = []
        foreach ($results as $key => $result) {
            $entryArray = $entryArrays[$key];
            $entryArray['uuid'] = $key;

            if ($result->getReasonPhrase() === 'OK') {
                $xmlDoc = $result->getBody()->getContents();
                $entryArray['xml_document'] = $xmlDoc;
            }

            $entryRecords[] = $entryArray;
        }

        $entries = Entry::insert($entryRecords);
    }
}

