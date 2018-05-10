<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WebSubTest extends TestCase
{

    /**
     * Subscribe check test.
     * Set 'hub.verify_token'
     *
     * @return void
     */
    public function testSubscribeCheckSuccess()
    {
        $response = $this->call('GET', '/hooks/websub/subscriber', [
                                    'hub_mode' => 'subscribe',
                                    'hub_verify_token' => 'testwebsub',
                                    'hub_challenge' => 'test'
                                ]);

        $response->assertSeeText('test');
    }

    public function testSubscribeCheckOldPathSuccess()
    {
        $response = $this->call('GET', '/hooks/push/subscriber', [
                                    'hub_mode' => 'subscribe',
                                    'hub_verify_token' => 'testwebsub',
                                    'hub_challenge' => 'test'
                                ]);

        $response->assertSeeText('test');
    }

    /**
     * Subscribe check test.
     * Set an incorrect value to 'hub.verify_token'
     *
     * @return void
     */
    public function testSubscribeCheckIncorrectToken()
    {
        $response = $this->call('GET', '/hooks/websub/subscriber', [
                                    'hub_mode' => 'subscribe',
                                    'hub.verify_token' => 'testfail'
                                ]);

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
        $response = $this->call('GET', '/hooks/websub/subscriber', [
                                    'hub_mode' => 'subscribe'
                                ]);

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
        $response = $this->call('GET', '/hooks/websub/subscriber');

        $response->assertNotFound();
    }

}
