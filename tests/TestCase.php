<?php

namespace Konsulting\Laravel\EditorStamps\Tests;

use Illuminate\Support\Facades\Schema;
use Konsulting\Laravel\EditorStamps\ServiceProvider;
use Konsulting\Laravel\EditorStamps\Tests\Support\TestUser;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('auth.providers.users.model', TestUser::class);
    }

    /**
     * The users table the creator/updater relations resolve against.
     */
    protected function createUsersTable()
    {
        Schema::create('users', function ($table) {
            $table->increments('id');
            $table->string('name');
        });
    }
}
