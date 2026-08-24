<?php

namespace Konsulting\Laravel\EditorStamps\Tests;

use Illuminate\Database\Schema\Blueprint as LaravelBlueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Konsulting\Laravel\EditorStamps\Blueprint as PackageBlueprint;
use Konsulting\Laravel\EditorStamps\Schema as PackageSchema;

class SchemaHelpersTest extends TestCase
{
    public function test_the_macro_style_adds_the_columns()
    {
        Schema::create('articles', function (LaravelBlueprint $table) {
            $table->increments('id');
            $table->editorStamps();
        });

        $this->assertTrue(Schema::hasColumn('articles', 'created_by'));
        $this->assertTrue(Schema::hasColumn('articles', 'updated_by'));
    }

    public function test_the_legacy_style_adds_the_columns()
    {
        PackageSchema::connection(null)->create('articles', function (PackageBlueprint $table) {
            $table->increments('id');
            $table->editorStamps();
        });

        $this->assertTrue(Schema::hasColumn('articles', 'created_by'));
        $this->assertTrue(Schema::hasColumn('articles', 'updated_by'));
    }

    /**
     * The columns default to 0 rather than being nullable, so that rows created
     * before the columns existed carry a concrete "not recorded" value.
     */
    public function test_the_columns_default_to_zero()
    {
        Schema::create('articles', function (LaravelBlueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->editorStamps();
        });

        DB::table('articles')->insert(['title' => 'Untouched']);

        $row = DB::table('articles')->first();

        $this->assertSame(0, (int) $row->created_by);
        $this->assertSame(0, (int) $row->updated_by);
    }

    public function test_drop_editor_stamps_removes_both_columns()
    {
        Schema::create('articles', function (LaravelBlueprint $table) {
            $table->increments('id');
            $table->editorStamps();
        });

        Schema::table('articles', function (LaravelBlueprint $table) {
            $table->dropEditorStamps();
        });

        $this->assertFalse(Schema::hasColumn('articles', 'created_by'));
        $this->assertFalse(Schema::hasColumn('articles', 'updated_by'));
        $this->assertTrue(Schema::hasColumn('articles', 'id'));
    }

    public function test_the_legacy_style_can_drop_the_columns()
    {
        PackageSchema::connection(null)->create('articles', function (PackageBlueprint $table) {
            $table->increments('id');
            $table->editorStamps();
        });

        PackageSchema::connection(null)->table('articles', function (PackageBlueprint $table) {
            $table->dropEditorStamps();
        });

        $this->assertFalse(Schema::hasColumn('articles', 'created_by'));
        $this->assertFalse(Schema::hasColumn('articles', 'updated_by'));
    }

    /**
     * Both styles must be usable in the same application - migrations written
     * against either one run in the same migrate:fresh.
     */
    public function test_both_styles_work_side_by_side()
    {
        Schema::create('macro_table', function (LaravelBlueprint $table) {
            $table->increments('id');
            $table->editorStamps();
        });

        PackageSchema::connection(null)->create('legacy_table', function (PackageBlueprint $table) {
            $table->increments('id');
            $table->editorStamps();
        });

        foreach (['macro_table', 'legacy_table'] as $table) {
            $this->assertTrue(Schema::hasColumn($table, 'created_by'), "{$table} is missing created_by");
            $this->assertTrue(Schema::hasColumn($table, 'updated_by'), "{$table} is missing updated_by");
        }
    }
}
