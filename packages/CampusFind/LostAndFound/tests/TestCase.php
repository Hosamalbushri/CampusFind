<?php

namespace CampusFind\LostAndFound\Tests;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    private ?string $originalDefaultConnection = null;

    private ?string $originalTimezone = null;

    private bool $createdInstalledFile = false;

    protected function setUp(): void
    {
        parent::setUp();

        $installedPath = storage_path('installed');
        if (! file_exists($installedPath)) {
            touch($installedPath);
            $this->createdInstalledFile = true;
        }

        $this->originalTimezone = config('app.timezone');
        config()->set('app.timezone', 'UTC');
        date_default_timezone_set('UTC');

        $this->originalDefaultConnection = DB::getDefaultConnection();

        config()->set('database.connections.lost_and_found_test', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);

        DB::setDefaultConnection('lost_and_found_test');
        Artisan::call('migrate', ['--database' => 'lost_and_found_test', '--force' => true]);

        if (! DB::table('roles')->where('id', 1)->exists()) {
            DB::table('roles')->insert([
                'id' => 1,
                'name' => 'Administrator',
                'permission_type' => 'all',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    protected function tearDown(): void
    {
        if ($this->createdInstalledFile && file_exists(storage_path('installed'))) {
            unlink(storage_path('installed'));
        }

        if ($this->originalTimezone !== null) {
            config()->set('app.timezone', $this->originalTimezone);
            date_default_timezone_set($this->originalTimezone);
        }

        if ($this->originalDefaultConnection !== null) {
            DB::setDefaultConnection($this->originalDefaultConnection);
            DB::purge('lost_and_found_test');
        }

        parent::tearDown();
    }
}
