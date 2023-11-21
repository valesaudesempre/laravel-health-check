<?php

namespace ValeSaude\LaravelHealthCheck\Tests;

use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use ValeSaude\LaravelHealthCheck\LaravelHealthCheckServiceProvider;

class TestCase extends OrchestraTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        # Setup default database to use sqlite :memory:
        $this->app['config']->set('database.default', 'testbench');
        $this->app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function setupQueueTables(): void
    {
        Schema::connection('testbench')->create('queues', function ($table) {
            $table->increments('id');
            $table->string('queue');
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });
    }

    protected function teardownQueueTables(): void
    {
        Schema::connection('testbench')->dropIfExists('queues');
    }

    protected function getPackageProviders($app): array
    {
        return [
            LaravelHealthCheckServiceProvider::class,
        ];
    }
}