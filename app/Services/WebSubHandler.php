<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Carbon\Carbon;
use GuzzleHttp\Promise;
use GuzzleHttp\Psr7;
use App\Eloquents\Feed;
use App\Eloquents\Entry;
use App\Eloquents\EntryDetail;
use Illuminate\Support\Collection;

class WebSubHandler
{
    /**
     * Verify feed's signature.
     *
     * @param  string $requestBody
     * @param  string? $hubSignature
    **/
    public static function verifySignature(string $requestBody, string $hubSignature = null) : bool
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
     *
     * @param  string? $hubMode
     * @param  string? $hubVerifyToken
    **/
    public static function verifyToken(string $hubMode = null, string $hubVerifyToken = null) : bool
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
     *
     * @param  array $feed
     * @return void
    **/
    public static function saveFeedAndEntries(array $feed)
    {
        $feedUuid = collect(explode(':', $feed['id']))->last();

        WebSubHandler::saveFeed($feedUuid, $feed);
        WebSubHandler::saveEntries($feedUuid, $feed['entry']);
    }

    /**
     * Save feed.
     *
     * @param  string $feedUuid
     * @param  array $feed
     * @return void
    **/
    private static function saveFeed(string $feedUuid, array $feed)
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
     *
     * @param  string $feedUuid
     * @param  array $entries
     * @return void
    **/
    private static function saveEntries(string $feedUuid, array $entries)
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

        $entryRecords = $results->map(function ($result, $key) use ($entryArrays) {
            $entryArray = $entryArrays[$key];

            if ($result->getReasonPhrase() === 'OK') {
                $xmlDoc = $result->getBody()->getContents();
                \Storage::put('entry/'.$key, $xmlDoc);
            }

            return $entryArray;
        })->values()->all();

        EntryDetail::insert($entryRecords);
    }

    /**
     * Parse entry.
     *
     * @param  array $entry
    **/
    private static function parseEntry(array $entry) : array
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
     *
     * @param  array $promises
    **/
    private static function fetchXmlDocument(array $promises) : Collection
    {
        return collect(Promise\settle($promises)->wait())->map(function ($obj, $key) {
            if ($obj['state'] === 'fulfilled') {
                return $obj['value'];
            }

            if ($obj['state'] === 'rejected') {
                return new Psr7\Response($obj['reason']->getCode());
            }

            return new Psr7\Response(0);
        });
    }
}