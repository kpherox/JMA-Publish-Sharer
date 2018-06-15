<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use GuzzleHttp\Promise;
use GuzzleHttp\Psr7;
use App\Eloquents\Feed;
use App\Eloquents\Entry;
use App\Services\WebSubHandler;

class WebSubController extends Controller
{
    /**
     * Subscribe Check JMA
     */
    public function subscribeCheck(Request $request) : Response
    {
        // Subscribe check
        try {
            WebSubHandler::verifyToken($request->hub_mode, $request->hub_verify_token);
            \Log::info('Success subscribe check');
        } catch (\Exception $e) {
            \Log::info('Failed subscribe check');
            abort(403, $e->getMessage());
        }

        return response($request->hub_challenge, 200)->header('Content-Type', 'text/plain');
    }

    /**
     * Recive JMA Publish
     *
     * @return void
     */
    public function receiveFeed(Request $request)
    {
        // Xml parse
        $content = $request->getContent();

        try {
            WebSubHandler::verifySignature($content, $request->header('x-hub-signature'));
            \Log::debug('Success check hub signature');
        } catch (\Exception $e) {
            abort(403, $e->getMessage());
        }

        libxml_use_internal_errors(true);
        $feedXml = simplexml_load_string($content);

        if (!$feedXml) {
            $message = 'Feed parse error';
            \Log::warning($message);

            foreach(libxml_get_errors() as $error) {
                \Log::warning(trim($error->message));
            }

            libxml_clear_errors();
            abort(403, $message);
        }
        \Log::debug('Success feed parse');

        $now = Carbon::now();
        $now->setTimezone(config('app.timezone'));

        $feed = json_decode(json_encode($feedXml), true);
        $feedUuid = collect(explode(':', $feed['id']))->last();
        $feedUpdated = Carbon::parse($feed['updated']);
        $feedUpdated->setTimezone(config('app.timezone'));
        $links = collect($feed['link'])
            ->map(function($item) {return $item['@attributes'];})
            ->pluck('href', 'rel');
        $feedUrl = $links['self'];
        $feeds = Feed::firstOrNew([
            'uuid' => $feedUuid,
            'url' => $feedUrl
        ]);
        $feeds->updated = $feedUpdated;
        $feeds->save();

        // Fetch JMA xml
        $entryArrays = [];
        $promises = [];
        $results  = [];

        if (Arr::isAssoc($feed['entry'])) {
            $feed['entry'] = [$feed['entry']];
        }

        foreach ($feed['entry'] as $entry) {
            $entryUuid = collect(explode(':', $entry['id']))->last();
            $kindOfInfo = $entry['title'];
            $observatory = collect($entry['author'])->get('name');
            $headline = $entry['content'];
            $updated = Carbon::parse($entry['updated']);
            $updated->setTimezone(config('app.timezone'));
            $url = $entry['link']['@attributes']['href'];

            $promises[$entryUuid] = \Guzzle::getAsync($url);

            $entryArrays[$entryUuid] = [
                'kind_of_info' => $kindOfInfo,
                'feed_uuid' => $feedUuid,
                'observatory_name' => $observatory,
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

        Entry::insert($entryRecords);
    }
}

