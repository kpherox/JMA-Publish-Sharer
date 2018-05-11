<?php

namespace App\Http\Controllers;

use Log;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Eloquents\Feed;
use App\Eloquents\Entry;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use GuzzleHttp\Psr7;

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

        if (config('app.isUseWebSubVerifyToken')) {
            $hubVerifyToken = $request->hub_verify_token;
            abort_if(empty($hubVerifyToken), 403, 'Not exist hub.verify_token');
            abort_if($hubVerifyToken != config('app.websubVerifyToken'), 403, 'Incorrect hub.verify_token');
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

        if (config('app.isUseWebSubVerifyToken')) {
            $hubSignature = $request->header('x-hub-signature');
            abort_if(empty($hubSignature), 403, 'Not exist x-hub-signature header');

            $signature = collect(explode('=',$hubSignature));
            abort_if($signature->count() !== 2, 403, 'Invalid x-hub-signature header');

            $hash = hash_hmac($signature->first(),$content,config('app.websubVerifyToken'));
            abort_if($signature->last() !== $hash, 403, 'Invalid hub signature');

            Log::debug('Success check hub signature');
        }

        if (false === ($feed = simplexml_load_string($content))) {
            $message = "Feed Parse ERROR";
            Log::error($message.": ".$content);
            return $message;
        }
        Log::debug('Success feed parse');

        $now = Carbon::now();
        $now->setTimezone(config('app.timezone'));

        $feedUuid = collect(explode(':', (string)$feed->id))->last();
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
            $entryUuid = collect(explode(':', (string)$entry->id))->last();
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
                    $results[$key] = new Psr7\Response($obj['reason']->getCode());
                    break;
                default:
                    $results[$key] = new Psr7\Response(0);
            }
        }

        $entryRecords = [];
        foreach ($results as $key => $result) {
            $entryArray = $entryArrays[$key];
            $entryArray['uuid'] = $key;
            $entryArray['created_at'] = $now;
            $entryArray['updated_at'] = $now;

            if ($result->getReasonPhrase() === 'OK') {
                $xmlDoc = $result->getBody()->getContents();
                $entryArray['xml_document'] = $xmlDoc;
            }

            $entryRecords[] = $entryArray;
        }

        $entries = Entry::insert($entryRecords);
    }
}

