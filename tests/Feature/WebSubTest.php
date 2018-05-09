<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WebSubTest extends TestCase
{

    /**
     * Subscribe check test.
     * unset 'X-Hub-Mode'
     *
     * @return void
     */
    public function testSubscribeCheck()
    {
        $response = $this->get('/hooks/websub/subscriber');

        $response->assertStatus(404);
    }

    public function testSubscribeCheckOldPath()
    {
        $response = $this->get('/hooks/push/subscriber');

        $response->assertStatus(404);
    }

}
