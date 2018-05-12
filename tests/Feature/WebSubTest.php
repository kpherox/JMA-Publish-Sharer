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

    /**
     * Receive feed test.
     * Success pattarn.
     *
     * @return void
     */
    public function testReceiveFeedSuccess()
    {
        \Guzzle::shouldReceive('getAsync')
                      ->once()
                      ->with('http://*/*/8e55b8d8-518b-3dc9-9156-7e87c001d7b5.xml')
                      ->andReturn(new Promise\FulfilledPromise(new Psr7\Response(200, [], '<?xml', 1.1)));
        \Guzzle::shouldReceive('getAsync')
                      ->once()
                      ->with('http://*/*/b60694a6-d389-3194-a051-092ee9b2c474.xml')
                      ->andReturn(new Promise\FulfilledPromise(new Psr7\Response(200, [], '<?xml', 1.1)));

        $atomFeed = file_get_contents('tests/SampleData/jmaxml_atomfeed.xml');

        $hash = hash_hmac('sha1', $atomFeed, $this->verifyToken);

        $headers = [
            'HTTP_user-agent' => 'AppEngine-Google; (+http://code.google.com/appengine; appid: s~alert-hub)',
            'HTTP_content-type' => 'application/atom+xml',
            'HTTP_x-hub-signature' => 'sha1='.$hash
        ];

        $response = $this->call('POST', self::$websubEndpoint, [], [], [], $headers, $atomFeed);

        $response->assertSuccessful();
    }

    /**
     * Subscribe check test.
     * Success pattarn.
     *
     * @return void
     */
    public function testSubscribeCheckSuccess()
    {
        $challengeValue = 'test';
        $parameter = [
            'hub_mode' => 'subscribe',
            'hub_verify_token' => $this->verifyToken,
            'hub_challenge' => $challengeValue,
        ];

        $response = $this->call('GET', self::$websubEndpoint, $parameter);

        $response->assertSuccessful()
            ->assertSeeText($challengeValue);
    }

    public function testSubscribeCheckOldPathSuccess()
    {
        $challengeValue = 'test';
        $parameter = [
            'hub_mode' => 'subscribe',
            'hub_verify_token' => $this->verifyToken,
            'hub_challenge' => $challengeValue
        ];

        $response = $this->call('GET', self::$websubOldEndpoint, $parameter);

        $response->assertSuccessful()
            ->assertSeeText($challengeValue);
    }

    /**
     * Subscribe check test.
     * Incorrect value to 'hub.verify_token'
     *
     * @return void
     */
    public function testSubscribeCheckIncorrectToken()
    {
        $parameter = [
            'hub_mode' => 'subscribe',
            'hub_verify_token' => 'testfail',
            'hub_challenge' => 'test'
        ];

        $response = $this->call('GET', self::$websubEndpoint, $parameter);

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
        $response = $this->call('GET', self::$websubEndpoint, ['hub_mode' => 'subscribe']);

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
        $response = $this->get(self::$websubEndpoint);

        $response->assertNotFound();
    }

}
