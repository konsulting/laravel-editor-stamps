<?php

namespace Konsulting\Laravel\EditorStamps\Tests;

use Illuminate\Database\Schema\Blueprint as LaravelBlueprint;
use Konsulting\Laravel\EditorStamps\Blueprint as PackageBlueprint;

/**
 * Both of the package's APIs must be registered together.
 *
 * Before 1.2.1 a Laravel version check registered one or the other, so an
 * application using the style that had not been registered broke during
 * migrations. These tests exist to stop that recurring.
 */
class ServiceProviderTest extends TestCase
{
    public function test_it_binds_the_custom_schema_builder()
    {
        $this->assertTrue($this->app->bound('db.custom_schema'));
    }

    public function test_it_registers_the_blueprint_macros()
    {
        $this->assertTrue(LaravelBlueprint::hasMacro('editorStamps'));
        $this->assertTrue(LaravelBlueprint::hasMacro('dropEditorStamps'));
    }

    public function test_the_custom_schema_builder_resolves_to_a_schema_builder()
    {
        $this->assertInstanceOf(
            \Illuminate\Database\Schema\Builder::class,
            $this->app->make('db.custom_schema')
        );
    }

    public function test_the_custom_schema_builder_produces_the_package_blueprint()
    {
        $captured = null;

        $this->app->make('db.custom_schema')->create('probe', function ($table) use (&$captured) {
            $captured = $table;
            $table->increments('id');
        });

        $this->assertInstanceOf(PackageBlueprint::class, $captured);
    }

    /**
     * The package's Blueprint inherits the macros from its parent. If it ever
     * declares its own editorStamps() again, that real method wins over the
     * macro - which is safe, but means the two could drift apart.
     */
    public function test_the_package_blueprint_inherits_the_macros()
    {
        $this->assertTrue(PackageBlueprint::hasMacro('editorStamps'));
        $this->assertFalse(
            method_exists(PackageBlueprint::class, 'editorStamps'),
            'Blueprint declares its own editorStamps(); the column logic now exists twice.'
        );
    }
}
