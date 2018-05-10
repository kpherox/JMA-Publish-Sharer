<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
        $atomFeed = '<?xml version="1.0" encoding="utf-8"?><feed xmlns="http://www.w3.org/2005/Atom" xml:lang="ja"><title>JMAXML publishing feed</title><subtitle>this feed is published by JMA</subtitle><updated>2012-08-15T07:01:01+09:00</updated><id>urn:uuid:be4342e2-ff73-363c-a3ed-66e05e977224</id><link rel="related" href="http://www.jma.go.jp/" /><link rel="self" href="http://xml.kishou.go.jp/*/*.xml" /><link rel="hub" href="http://alert-hub.appspot.com/" /><rights>Published by Japan Meteorological Agency</rights><entry><title>気象警報・注意報</title><id>urn:uuid:8e55b8d8-518b-3dc9-9156-7e87c001d7b5</id><updated>2012-08-15T07:00:00+09:00</updated><author><name>富山地方気象台</name></author><link href="http://*/*/8e55b8d8-518b-3dc9-9156-7e87c001d7b5.xml" type="application/xml" /><content type="text">【富山県気象警報・注意報】富山県では、強風、高波に注意してください。</content></entry><entry><title>気象警報・注意報</title><id>urn:uuid:b60694a6-d389-3194-a051-092ee9b2c474</id><updated>2012-08-15T07:00:00+09:00</updated><author><name>京都地方気象台</name></author><link href="http://*/*/b60694a6-d389-3194-a051-092ee9b2c474.xml" type="application/xml"/><content type="text">【京都府気象警報・注意報】京都府では、１５日昼過ぎから高波に注意して下さい</content></entry></feed>';
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
