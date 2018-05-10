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
            self::$migrated = true;
        }
    }

    public function tearDown()
    {
        foreach (\DB::select('SHOW TABLES') as $table) {
            $columnName = 'Tables_in_'.\DB::connection('')->getDatabaseName();
            $tableName = $table->$columnName;
            if ($tableName === 'migrations') continue;
            \DB::statement('TRUNCATE TABLE `' . $tableName . '`');
        }
        parent::tearDown();
    }

}
