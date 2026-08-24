<?php

namespace Konsulting\Laravel\EditorStamps;

use Illuminate\Database\Schema\Blueprint as IlluminateBlueprint;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        // Legacy API: the package's own Schema facade and Blueprint subclass.
        $this->app->bind('db.custom_schema', fn () => Schema::customizedSchemaBuilder());

        // Current API: the same columns on Laravel's own Blueprint. The subclass
        // above inherits these, so both styles work in the same application.
        IlluminateBlueprint::macro('editorStamps', function () {
            $this->integer('created_by')->unsigned()->default(0);
            $this->integer('updated_by')->unsigned()->default(0);
        });

        IlluminateBlueprint::macro('dropEditorStamps', function () {
            $this->dropColumn('created_by');
            $this->dropColumn('updated_by');
        });
    }
}
