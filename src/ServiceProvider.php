<?php

namespace Konsulting\Laravel\EditorStamps;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->app->bind('db.custom_schema', fn() => Schema::customizedSchemaBuilder());
    }
}