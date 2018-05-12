<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use GuzzleHttp\Psr7;
use GuzzleHttp\Promise;

class WebSubTest extends TestCase
{

    private static $websubEndpoint = '/hooks/websub/subscriber';
    private static $websubOldEndpoint = '/hooks/push/subscriber';

    private $verifyToken = 'testwebsub';
    private $challengeValue = 'test';

    /**
     * Get 'GET query' parameters
     *
     * @return array
     */
    private function getParameters(String $status)
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
     * Receive feed test.
     * Success pattarn.
     *
     * @return void
     */
    public function testReceiveFeedSuccess()
    {
        $sampleData1 = file_get_contents('tests/SampleData/jmaxml_20180308_Samples/01_01_01_091210_VGSK50.xml');
        $sampleData2 = file_get_contents('tests/SampleData/jmaxml_20180308_Samples/01_01_02_091210_VGSK50.xml');

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

        $headers = [
            'HTTP_user-agent' => 'AppEngine-Google; (+http://code.google.com/appengine; appid: s~alert-hub)',
            'HTTP_content-type' => 'application/atom+xml',
            'HTTP_x-hub-signature' => 'sha1='.$hash
        ];

        $response = $this->call('POST', self::$websubEndpoint, [], [], [], $headers, $atomFeed);

        $response->assertSuccessful();
        $this
            ->assertDatabaseHas('entries', [
                'uuid' => '8e55b8d8-518b-3dc9-9156-7e87c001d7b5',
                'xml_document' => $sampleData1
            ])
            ->assertDatabaseHas('entries', [
                'uuid' => 'b60694a6-d389-3194-a051-092ee9b2c474',
                'xml_document' => $sampleData2
            ])
            ->assertDatabaseHas('feeds', [
                'uuid' => 'be4342e2-ff73-363c-a3ed-66e05e977224',
            ]);
    }

    /**
     * Subscribe check test.
     * Success pattarn.
     *
     * @return void
     */
    public function testSubscribeCheckSuccess()
    {
        $response = $this->call('GET', self::$websubEndpoint, $this->getParameters('check_success'));

        $response
            ->assertSuccessful()
            ->assertSeeText($this->challengeValue);
    }

    public function testSubscribeCheckOldPathSuccess()
    {
        $response = $this->call('GET', self::$websubOldEndpoint, $this->getParameters('check_success'));

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
        $response = $this->call('GET', self::$websubEndpoint, $this->getParameters('incorrect_token'));

        $response->assertForbidden();
    }

    /**
     * Subscribe check test.
     * Does't set 'hub.verify_token'
     *
     * @return void
     */
    public function testSubscribeCheckNotExistToken()
    {
        $response = $this->call('GET', self::$websubEndpoint, $this->getParameters('not_exist_token'));

        $response->assertForbidden();
    }

    /**
     * Subscribe check test.
     * Doesn't set 'hub.mode'
     *
     * @return void
     */
    public function testSubscribeCheckNotFound()
    {
        $response = $this->call('GET', self::$websubEndpoint, $this->getParameters(''));

        $response->assertNotFound();
    }

}
