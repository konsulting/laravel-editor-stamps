<?php

namespace Konsulting\Laravel\EditorStamps;

use Illuminate\Support\Facades\Facade;
use Illuminate\Database\Schema\Builder;

/**
 * @see \Illuminate\Database\Schema\Builder
 */
class Schema extends Facade
{
    public static function customizedSchemaBuilder(?string $name = null): Builder
    {
        $builder = static::$app['db']->connection($name)->getSchemaBuilder();
        $builder->blueprintResolver(function ($table, $callback) {
            return new Blueprint($table, $callback);
        });

        return $builder;
    }

    /**
     * Get a schema builder instance for a connection.
     *
     * @param  string  $name
     * @return \Illuminate\Database\Schema\Builder
     */
    public static function connection($name)
    {
        return static::customizedSchemaBuilder($name);
    }

    /**
     * Get a schema builder instance for the default connection.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'db.custom_schema';
    }
}
