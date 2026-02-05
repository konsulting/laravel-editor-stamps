<?php

namespace Konsulting\Laravel\EditorStamps;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        if(app()->version() <= 12) {
            $this->app->bind('db.custom_schema', fn() => Schema::customizedSchemaBuilder());

            return;
        }

        \Illuminate\Database\Schema\Blueprint::macro('editorStamps', function () {
            $this->integer('created_by')->unsigned()->default(0);
            $this->integer('updated_by')->unsigned()->default(0);
        });

        \Illuminate\Database\Schema\Blueprint::macro('dropEditorStamps', function () {
            $this->dropColumn('created_by');
            $this->dropColumn('updated_by');
        });
    }
}