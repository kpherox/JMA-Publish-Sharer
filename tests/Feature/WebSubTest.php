<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use GuzzleHttp\Psr7;
use GuzzleHttp\Promise;
use App\Notifications\EntryReceived;
use App\Eloquents\LinkedSocialAccount;

class WebSubTest extends TestCase
{

    private static $websubEndpoint = '/hooks/websub/subscriber';

    private $verifyToken = 'testwebsub';
    private $challengeValue = 'test';

    /**
     * Get query parameters for subscribe check test.
     *
     * @return array
     */
    private function getParameters(string $status)
    {
        $result = [];

        switch ($status) {
            case 'check_success':
                $result = [
                    'hub_mode' => 'subscribe',
                    'hub_verify_token' => $this->verifyToken,
                    'hub_challenge' => $this->challengeValue,
                ];
                break;
            case 'incorrect_token':
                $result = [
                    'hub_mode' => 'subscribe',
                    'hub_verify_token' => 'testfail',
                    'hub_challenge' => $this->challengeValue,
                ];
                break;
            case 'not_exist_token':
                $result = [
                    'hub_mode' => 'subscribe',
                ];
                break;
            default:
                break;
        }

        return $result;
    }

    /**
     * Get headers for receive feed test.
     *
     * @return array
     */
    private function getHeaders(string $verbs = 'GET', string $signature = null)
    {
        $headers = [
            'HTTP_User-Agent' => 'AppEngine-Google; (+http://code.google.com/appengine; appid: s~alert-hub)'
        ];

        if (mb_strtoupper($verbs) === 'GET') {
            return $headers;
        };

        $headers['HTTP_Content-Type'] = 'application/atom+xml';

        if (!empty($signature)) {
            $headers['HTTP_X-Hub-Signature'] = $signature;
        };

        return $headers;
    }

    /**
     * Receive feed test.
     * Notification sent success.
     *
     * @return void
     */
    public function testEntryReceivedNotification()
    {
        \Notification::fake();

        \Storage::fake('local');

        $sampleData1 = file_get_contents('tests/SampleData/jmaxml_Samples/01_01_01_091210_VGSK50.xml');
        $sampleData2 = file_get_contents('tests/SampleData/jmaxml_Samples/01_01_02_091210_VGSK50.xml');

        \Guzzle::shouldReceive('getAsync')
                      ->once()
                      ->with('http://*/*/8e55b8d8-518b-3dc9-9156-7e87c001d7b5.xml')
                      ->andReturn(new Promise\FulfilledPromise(new Psr7\Response(200, [], $sampleData1)));
        \Guzzle::shouldReceive('getAsync')
                      ->once()
                      ->with('http://*/*/b60694a6-d389-3194-a051-092ee9b2c474.xml')
                      ->andReturn(new Promise\FulfilledPromise(new Psr7\Response(200, [], $sampleData2)));

        $atomFeed = file_get_contents('tests/SampleData/jmaxml_atomfeed.xml');

        $hash = hash_hmac('sha1', $atomFeed, $this->verifyToken);

        $response = $this->call('POST', self::$websubEndpoint, [], [], [], $this->getHeaders('POST', 'sha1='.$hash), $atomFeed);

        \Notification::assertSentTo(
            LinkedSocialAccount::where('provider_name', 'twitter')->first(),
            EntryReceived::class
        );

        \Notification::assertNotSentTo(
            LinkedSocialAccount::where('provider_name', 'github')->first(),
            EntryReceived::class
        );
    }

    /**
     * Receive feed test.
     * Success pattarn.
     *
     * @return void
     */
    public function testReceiveFeedSuccess()
    {
        \Notification::fake();
        \Storage::fake('local');

        $sampleData1 = file_get_contents('tests/SampleData/jmaxml_Samples/01_01_01_091210_VGSK50.xml');
        $sampleData2 = file_get_contents('tests/SampleData/jmaxml_Samples/01_01_02_091210_VGSK50.xml');

        \Guzzle::shouldReceive('getAsync')
                      ->once()
                      ->with('http://*/*/8e55b8d8-518b-3dc9-9156-7e87c001d7b5.xml')
                      ->andReturn(new Promise\FulfilledPromise(new Psr7\Response(200, [], $sampleData1)));
        \Guzzle::shouldReceive('getAsync')
                      ->once()
                      ->with('http://*/*/b60694a6-d389-3194-a051-092ee9b2c474.xml')
                      ->andReturn(new Promise\FulfilledPromise(new Psr7\Response(200, [], $sampleData2)));

        $atomFeed = file_get_contents('tests/SampleData/jmaxml_atomfeed.xml');

        $hash = hash_hmac('sha1', $atomFeed, $this->verifyToken);

        $response = $this->call('POST', self::$websubEndpoint, [], [], [], $this->getHeaders('POST', 'sha1='.$hash), $atomFeed);

        $response->assertSuccessful();
        $this
            ->assertDatabaseHas('entry_details', [
                'uuid' => '8e55b8d8-518b-3dc9-9156-7e87c001d7b5',
            ])
            ->assertDatabaseHas('entry_details', [
                'uuid' => 'b60694a6-d389-3194-a051-092ee9b2c474',
            ])
            ->assertDatabaseHas('feeds', [
                'uuid' => 'be4342e2-ff73-363c-a3ed-66e05e977224',
                'url' => 'http://xml.kishou.go.jp/*/*.xml'
            ]);

        \Storage::disk('local')->assertExists('entry/8e55b8d8-518b-3dc9-9156-7e87c001d7b5');
        \Storage::disk('local')->assertExists('entry/b60694a6-d389-3194-a051-092ee9b2c474');
    }

    /**
     * Receive feed test.
     * Feed xml parse error.
     *
     * @return void
     */
    public function testReceiveFeedXmlParseError()
    {
        $atomFeed = ' '.file_get_contents('tests/SampleData/jmaxml_atomfeed.xml');

        $hash = hash_hmac('sha1', $atomFeed, $this->verifyToken);

        $response = $this->call('POST', self::$websubEndpoint, [], [], [], $this->getHeaders('POST', 'sha1='.$hash), $atomFeed);

        $response
            ->assertForbidden()
            ->assertSeeText('XML Parse error');
    }

    /**
     * Receive feed test.
     * Invalid signature.
     *
     * @return void
     */
    public function testReceiveFeedInvalidSignature()
    {
        $atomFeed = file_get_contents('tests/SampleData/jmaxml_atomfeed.xml');

        $hash = hash_hmac('sha1', $atomFeed, $this->verifyToken);

        $response = $this->call('POST', self::$websubEndpoint, [], [], [], $this->getHeaders('POST', 'sha1=test'.$hash), $atomFeed);

        $response
            ->assertForbidden()
            ->assertSeeText('Invalid signature');
    }

    /**
     * Receive feed test.
     * Invalid X-Hub-Signature header.
     *
     * @return void
     */
    public function testReceiveFeedInvalidSignatureHeader()
    {
        $atomFeed = file_get_contents('tests/SampleData/jmaxml_atomfeed.xml');

        $hash = hash_hmac('sha1', $atomFeed, $this->verifyToken);

        $response = $this->call('POST', self::$websubEndpoint, [], [], [], $this->getHeaders('POST', $hash), $atomFeed);

        $response
            ->assertForbidden()
            ->assertSeeText('Invalid hubSignature');
    }

    /**
     * Receive feed test.
     * Doesn't set X-Hub-Signature header.
     *
     * @return void
     */
    public function testReceiveFeedNotExistSignature()
    {
        $atomFeed = file_get_contents('tests/SampleData/jmaxml_atomfeed.xml');

        $response = $this->call('POST', self::$websubEndpoint, [], [], [], $this->getHeaders('POST'), $atomFeed);

        $response
            ->assertForbidden()
            ->assertSeeText('Not exist x-hub-signature header');
    }

    /**
     * Subscribe check test.
     * Success pattarn.
     *
     * @return void
     */
    public function testSubscribeCheckSuccess()
    {
        $response = $this->call('GET', self::$websubEndpoint, $this->getParameters('check_success'), [], [], $this->getHeaders());

        $response
            ->assertSuccessful()
            ->assertSeeText($this->challengeValue);
    }

    /**
     * Subscribe check test.
     * Incorrect value to 'hub.verify_token'
     *
     * @return void
     */
    public function testSubscribeCheckIncorrectToken()
    {
        $response = $this->call('GET', self::$websubEndpoint, $this->getParameters('incorrect_token'), [], [], $this->getHeaders());

        $response
            ->assertForbidden()
            ->assertSeeText('Incorrect hub.verify_token');
    }

    /**
     * Subscribe check test.
     * Does't set 'hub.verify_token'
     *
     * @return void
     */
    public function testSubscribeCheckNotExistToken()
    {
        $response = $this->call('GET', self::$websubEndpoint, $this->getParameters('not_exist_token'), [], [], $this->getHeaders());

        $response
            ->assertForbidden()
            ->assertSeeText('Not exist hub.verify_token');
    }

    /**
     * Subscribe check test.
     * Doesn't set 'hub.mode'
     *
     * @return void
     */
    public function testSubscribeCheckNotExistMode()
    {
        $response = $this->call('GET', self::$websubEndpoint, $this->getParameters(''), [], [], $this->getHeaders());

        $response
            ->assertForbidden()
            ->assertSeeText('Not exist hub.mode');
    }

}
