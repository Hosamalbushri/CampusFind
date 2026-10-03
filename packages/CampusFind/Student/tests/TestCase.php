<?php

namespace CampusFind\Student\Tests;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    private ?string $originalDefaultConnection = null;

    private bool $createdInstalledFile = false;

    protected function setUp(): void
    {
        parent::setUp();

        $installedPath = storage_path('installed');
        if (! file_exists($installedPath)) {
            touch($installedPath);
            $this->createdInstalledFile = true;
        }

        $this->originalDefaultConnection = DB::getDefaultConnection();

        config()->set('database.connections.student_test', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);

        DB::setDefaultConnection('student_test');
        Artisan::call('migrate', ['--database' => 'student_test', '--force' => true]);
    }

    protected function tearDown(): void
    {
        if ($this->createdInstalledFile && file_exists(storage_path('installed'))) {
            unlink(storage_path('installed'));
        }

        if ($this->originalDefaultConnection !== null) {
            DB::setDefaultConnection($this->originalDefaultConnection);
            DB::purge('student_test');
        }

        parent::tearDown();
    }
}
