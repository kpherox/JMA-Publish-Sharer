<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public static $migrated = false;

    public function setUp()
    {
        parent::setUp();
        if (!self::$migrated) {
            \Artisan::call('migrate:refresh');
            \Artisan::call('db:seed');
            self::$migrated = true;
        }
    }

    public function tearDown()
    {
        \DB::table('feeds')->truncate();
        \DB::table('entries')->truncate();
        \DB::table('entry_details')->truncate();
        parent::tearDown();
    }

}
