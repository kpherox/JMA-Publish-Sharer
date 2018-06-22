<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Carbon\Carbon;
use GuzzleHttp\Promise;
use GuzzleHttp\Psr7;
use App\Eloquents\Feed;
use App\Eloquents\Entry;
use App\Eloquents\EntryDetail;

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

    /**
     * Save feed and entries.
    **/
    public static function saveFeedAndEntries(Array $feed = null)
    {
        $feedUuid = collect(explode(':', $feed['id']))->last();

        WebSubHandler::saveFeed($feedUuid, $feed);
        WebSubHandler::saveEntries($feedUuid, $feed['entry']);
    }

    /**
     * Save feed.
    **/
    private static function saveFeed(String $feedUuid, Array $feed = null)
    {
        $feeds = Feed::firstOrNew(['uuid' => $feedUuid]);

        $feedUpdated = Carbon::parse($feed['updated']);
        $feedUpdated->setTimezone(config('app.timezone'));

        $feeds->updated = $feedUpdated;

        $links = collect($feed['link'])
                    ->map(function($item) {return $item['@attributes'];})
                    ->pluck('href', 'rel');
        $feedUrl = $links['self'];

        $feeds->url = $feedUrl;

        $feeds->save();
    }

    /**
     * Save entries.
    **/
    private static function saveEntries(String $feedUuid, Array $entries = null)
    {
        if (Arr::isAssoc($entries)) {
            $entries = [$entries];
        }

        // Fetch JMA xml
        $entryArrays = [];
        $promises = [];
        $results  = [];

        foreach ($entries as $entry) {
            $parseedEntry = self::parseEntry($entry);

            $entryUuid = $parseedEntry['uuid'];

            $promises[$entryUuid] = $parseedEntry['promise'];

            $entryArray = $parseedEntry['entry'];
            $entryArray['feed_uuid'] = $feedUuid;
            $entry = Entry::firstOrCreate($entryArray);

            $entryDetail = $parseedEntry['entryDetail'];
            $entryDetail['entry_id'] = $entry->id;
            $entryDetail['created_at'] = $entry->created_at;
            $entryDetail['updated_at'] = $entry->updated_at;
            $entryArrays[$entryUuid] = $entryDetail;
        }

        $results = self::fetchXmlDocument($promises);


        $entryRecords = [];
        foreach ($results as $key => $result) {
            $entryArray = $entryArrays[$key];

            if ($result->getReasonPhrase() === 'OK') {
                $xmlDoc = $result->getBody()->getContents();
                \Storage::put('entry/'.$key, $xmlDoc);
            }

            $entryRecords[] = $entryArray;
        }

        EntryDetail::insert($entryRecords);
    }

    /**
     * Parse entry.
    **/
    private static function parseEntry(Array $entry = null) : Array
    {
        $entryUuid = collect(explode(':', $entry['id']))->last();

        $updated = Carbon::parse($entry['updated']);
        $updated->setTimezone(config('app.timezone'));

        $url = $entry['link']['@attributes']['href'];

        return [
            'uuid' => $entryUuid,
            'promise' => \Guzzle::getAsync($url),
            'entry' => [
                'observatory_name' => collect($entry['author'])->get('name'),
                'headline' => $entry['content'],
                'updated' => $updated,
            ],
            'entryDetail' => [
                'uuid' => $entryUuid,
                'kind_of_info' => $entry['title'],
                'url' => $url,
            ],
        ];
    }

    /**
     * Fetch xml document from JMA.
    **/
    private static function fetchXmlDocument(Array $promises) : Array
    {
        $results = [];

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
                    break;
            }
        }

        return $results;
    }
}
