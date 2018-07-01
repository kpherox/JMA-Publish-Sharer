<?php

namespace App\Services;

use Carbon\Carbon;
use GuzzleHttp\Psr7;
use App\Eloquents\Feed;
use GuzzleHttp\Promise;
use App\Eloquents\Entry;
use Illuminate\Support\Arr;
use App\Eloquents\EntryDetail;
use Illuminate\Support\Collection;

class WebSubHandler
{
    /**
     * Verify feed's signature.
     *
     * @param  string $requestBody
     * @param  string? $hubSignature
     */
    public static function verifySignature(string $requestBody, string $hubSignature = null) : bool
    {
        if (! config('app.isUseWebSubVerifyToken')) {
            return true;
        }

        if (empty($hubSignature)) {
            throw new \Exception('Not exist x-hub-signature header');
        }

        $signature = collect(explode('=', $hubSignature));
        if ($signature->count() !== 2) {
            throw new \Exception('Invalid hubSignature');
        }

        $hash = hash_hmac($signature->first(), $requestBody, config('app.websubVerifyToken'));
        if ($signature->last() !== $hash) {
            throw new \Exception('Invalid signature');
        }

        return true;
    }

    /**
     * Verify token for subscribe check.
     *
     * @param  string? $hubMode
     * @param  string? $hubVerifyToken
     */
    public static function verifyToken(string $hubMode = null, string $hubVerifyToken = null) : bool
    {
        if ($hubMode !== 'subscribe' && $hubMode !== 'unsubscribe') {
            throw new \Exception('Not exist hub.mode');
        }
        \Log::notice($hubMode);

        if (! config('app.isUseWebSubVerifyToken')) {
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
     */
    public static function saveFeedAndEntries(array $feedArray)
    {
        $feed = self::saveFeed($feedArray['id'], $feedArray['updated'], $feedArray['link']);
        $entries = self::saveEntries($feedArray['entry']);

        $entries->each(function ($entry) use ($feed, &$promises) {
            $feed->entries()->save($entry['entry']);

            $promises[] = $entry->except(['except'])->all();
        });

        self::saveDetailsXml(collect($promises));
    }

    /**
     * Save feed.
     *
     * @param  string $uuidString
     * @param  string $updatedString
     * @param  array $links
     */
    private static function saveFeed(string $uuidString, string $updatedString, array $links) : Feed
    {
        $uuid = collect(explode(':', $uuidString))->last();
        $feed = Feed::firstOrNew(['uuid' => $uuid]);

        $updated = Carbon::parse($updatedString);
        $updated->setTimezone(config('app.timezone'));

        $feed->updated = $updated;

        $url = collect($links)->map(function ($item) {
            return $item['@attributes'];
        })->pluck('href', 'rel');

        $feed->url = $url['self'];

        $feed->save();

        return $feed;
    }

    /**
     * Save entries.
     *
     * @param  array $entries
     */
    private static function saveEntries(array $entries) : Collection
    {
        if (Arr::isAssoc($entries)) {
            $entries = [$entries];
        }

        return collect($entries)->map(function ($entryArray) {
            $parseedEntry = self::parseEntry($entryArray);

            $entryArray = $parseedEntry['entry'];
            $entry = Entry::firstOrCreate($parseedEntry['entry']);

            $entryDetail = EntryDetail::create($parseedEntry['entryDetail']);
            $entry->entryDetails()->save($entryDetail);

            return collect([
                'entry' => $entry,
                'detail' => $entryDetail,
                'promise' => $parseedEntry['promise'],
            ]);
        });
    }

    /**
     * Save entry_details xml document.
     *
     * @param  \Illuminate\Support\Collection $promises
     * @return void
     */
    private static function saveDetailsXml(Collection $promises)
    {
        $results = self::fetchXmlDocument($promises->map(function ($value) {
            return $value['promise'];
        })->all());

        $results->each(function ($result, $key) use ($promises) {
            if ($result->getReasonPhrase() === 'OK') {
                $xmlDoc = $result->getBody()->getContents();
                $detail = $promises[$key]['detail'];
                $detail->xml_file = $xmlDoc;
            }
        });
    }

    /**
     * Parse entry.
     *
     * @param  array $entry
     */
    private static function parseEntry(array $entry) : array
    {
        $uuid = collect(explode(':', $entry['id']))->last();

        $updated = Carbon::parse($entry['updated']);
        $updated->setTimezone(config('app.timezone'));

        $url = $entry['link']['@attributes']['href'];

        return [
            'promise' => \Guzzle::getAsync($url),
            'entry' => [
                'observatory_name' => collect($entry['author'])->get('name'),
                'headline' => $entry['content'],
                'updated' => $updated,
            ],
            'entryDetail' => [
                'uuid' => $uuid,
                'kind_of_info' => $entry['title'],
                'url' => $url,
            ],
        ];
    }

    /**
     * Fetch xml document from JMA.
     *
     * @param  array $promises
     */
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
