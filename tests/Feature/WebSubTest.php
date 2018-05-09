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
                                    'hub_verify_token' => 'testwebsub'
                                ]);

        $response->assertStatus(200);
    }

    public function testSubscribeCheckOldPathSuccess()
    {
        $response = $this->call('GET', '/hooks/push/subscriber', [
                                    'hub_mode' => 'subscribe',
                                    'hub_verify_token' => 'testwebsub'
                                ]);

        $response->assertStatus(200);
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

        $response->assertStatus(403);
    }

    public function testSubscribeCheckOldPathIncorrecetToken()
    {
        $response = $this->call('GET', '/hooks/push/subscriber', [
                                    'hub_mode' => 'subscribe',
                                    'hub.verify_token' => 'testfail'
                                ]);

        $response->assertStatus(403);
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

        $response->assertStatus(403);
    }

    public function testSubscribeCheckOldPathNotExistToken()
    {
        $response = $this->call('GET', '/hooks/push/subscriber', [
                                    'hub_mode' => 'subscribe'
                                ]);

        $response->assertStatus(403);
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

        $response->assertStatus(404);
    }

    public function testSubscribeCheckOldPathNotFound()
    {
        $response = $this->call('GET', '/hooks/push/subscriber');

        $response->assertStatus(404);
    }

}
