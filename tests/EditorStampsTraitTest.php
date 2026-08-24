<?php

namespace Konsulting\Laravel\EditorStamps\Tests;

use Illuminate\Database\Schema\Blueprint as LaravelBlueprint;
use Illuminate\Support\Facades\Schema;
use Konsulting\Laravel\EditorStamps\Tests\Support\Article;
use Konsulting\Laravel\EditorStamps\Tests\Support\SoftDeletingArticle;
use Konsulting\Laravel\EditorStamps\Tests\Support\TestUser;

class EditorStampsTraitTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->createUsersTable();

        Schema::create('articles', function (LaravelBlueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->editorStamps();
        });

        Schema::create('soft_articles', function (LaravelBlueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->softDeletes();
            $table->editorStamps();
        });
    }

    public function test_it_stamps_both_columns_on_create()
    {
        $user = $this->actingAsUser();

        $article = Article::create(['title' => 'First']);

        $this->assertSame($user->id, $article->created_by);
        $this->assertSame($user->id, $article->updated_by);
    }

    public function test_it_stamps_only_the_updater_on_update()
    {
        $author = $this->actingAsUser('Author');
        $article = Article::create(['title' => 'First']);

        $editor = $this->actingAsUser('Editor');
        $article->update(['title' => 'Revised']);

        $this->assertSame($author->id, $article->fresh()->created_by);
        $this->assertSame($editor->id, $article->fresh()->updated_by);
    }

    /**
     * Queue jobs, console commands and seeders run unauthenticated. The trait
     * must leave the columns alone rather than failing or writing a null.
     */
    public function test_it_leaves_the_columns_alone_when_nobody_is_authenticated()
    {
        $article = Article::create(['title' => 'From a queue job']);

        $this->assertSame(0, (int) $article->fresh()->created_by);
        $this->assertSame(0, (int) $article->fresh()->updated_by);
    }

    public function test_it_does_not_fail_updating_while_unauthenticated()
    {
        $user = $this->actingAsUser();
        $article = Article::create(['title' => 'First']);

        \Illuminate\Support\Facades\Auth::logout();
        $article->update(['title' => 'Revised by nobody']);

        $this->assertSame('Revised by nobody', $article->fresh()->title);
        $this->assertSame($user->id, $article->fresh()->updated_by);
    }

    /**
     * Known limitation, locked in here so a change to it is deliberate.
     *
     * The trait sets updated_by on the "deleting" event, but never saves. A
     * hard delete then removes the row, and SoftDeletes::runSoftDelete() issues
     * an update built from an explicit column list - deleted_at, plus
     * updated_at when the model is timestamped - so the assignment reaches the
     * in-memory model and is discarded.
     *
     * The delete is therefore recorded by deleted_at alone; updated_by still
     * names whoever last updated the row.
     */
    public function test_a_soft_delete_does_not_persist_the_updater()
    {
        $author = $this->actingAsUser('Author');
        $article = SoftDeletingArticle::create(['title' => 'Doomed']);

        $remover = $this->actingAsUser('Remover');
        $article->delete();

        // Set on the instance in memory...
        $this->assertSame($remover->id, $article->updated_by);

        // ...but not written to the row.
        $trashed = SoftDeletingArticle::withTrashed()->find($article->id);

        $this->assertNotNull($trashed->deleted_at);
        $this->assertSame($author->id, $trashed->updated_by);
        $this->assertSame($author->id, $trashed->created_by);
    }

    public function test_it_exposes_the_creator_and_updater_relations()
    {
        $author = $this->actingAsUser('Author');
        $article = Article::create(['title' => 'First']);

        $editor = $this->actingAsUser('Editor');
        $article->update(['title' => 'Revised']);

        $article = $article->fresh();

        $this->assertInstanceOf(TestUser::class, $article->creator);
        $this->assertSame('Author', $article->creator->name);
        $this->assertSame('Editor', $article->updater->name);
    }

    /**
     * The relations resolve against the configured user model rather than a
     * hard-coded App\Models\User.
     */
    public function test_the_relations_use_the_configured_user_model()
    {
        $this->actingAsUser();
        $article = Article::create(['title' => 'First']);

        $this->assertSame(
            config('auth.providers.users.model'),
            get_class($article->fresh()->creator)
        );
    }

    protected function actingAsUser($name = 'Test User')
    {
        $user = TestUser::create(['name' => $name]);

        $this->actingAs($user);

        return $user;
    }
}
