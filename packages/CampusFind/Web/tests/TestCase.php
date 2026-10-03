<?php

namespace CampusFind\Web\Tests;

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

        config()->set('database.connections.web_test', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);

        DB::setDefaultConnection('web_test');
        Artisan::call('migrate', ['--database' => 'web_test', '--force' => true]);
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
            DB::purge('web_test');
        }

        parent::tearDown();
    }
}
