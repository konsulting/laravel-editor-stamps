# Editor Stamps #

Records which user created and last updated a model.

Two independent pieces, usable together or on their own:

* a trait for your [Laravel](http://laravel.com) models, which stamps the authenticated user on save;
* schema helpers for your migrations, which add the columns the trait writes to.

## Compatibility ##

Laravel 9, 10, 11, 12 and 13.

The test suite passes against each of them: 9.52, 10.50, 11.56, 12.67 and
13.26. Laravel 9, 10 and 11 are out of upstream security support, so Composer
declines to install them under its default advisory policy; testing against
those three means relaxing that policy locally.

Laravel 7 and 8 also work for adding columns, but are not covered.

## Installation ##

```
composer require konsulting/laravel-editor-stamps
```

The service provider is auto-discovered. It registers both migration styles, so
nothing further is needed.

## Usage ##

### The trait ###

`use` it in your model:

```php
use Konsulting\Laravel\EditorStamps\EditorStamps;

class Article extends Model
{
    use EditorStamps;
}
```

While a user is authenticated, `created_by` is set on create and `updated_by`
on create and update. When no user is authenticated the model is left
untouched, so queue jobs, console commands and seeders are unaffected.

Two relations are provided, both resolving against
`config('auth.providers.users.model')`:

```php
$article->creator;   // the user who created it
$article->updater;   // the user who last updated it
```

Deletes are not stamped. The trait sets `updated_by` while handling the
`deleting` event but does not save, so a hard delete removes the row and a soft
delete writes only `deleted_at` — Laravel builds that update from an explicit
column list. After deleting, `updated_by` still names whoever last updated the
row.

### The migration columns ###

`editorStamps()` adds unsigned `created_by` and `updated_by` integer columns,
both defaulting to `0`. `dropEditorStamps()` removes them again. They are
available on Laravel's own Blueprint:

```php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

Schema::create('articles', function (Blueprint $table) {
    $table->increments('id');
    $table->editorStamps();
});
```

The columns default to `0` rather than being nullable, so existing rows get a
concrete value. `0` means "not recorded" and matches no real user.

### The Schema facade and Blueprint (legacy) ###

Earlier versions provided a replacement Schema facade and Blueprint subclass,
aliased in `config/app.php`:

```php
'aliases' => Facade::defaultAliases()->merge([
    'Schema' => \Konsulting\Laravel\EditorStamps\Schema::class,
])->toArray(),
```

```php
use Konsulting\Laravel\EditorStamps\Blueprint;

Schema::create('articles', function (Blueprint $table) {
    $table->editorStamps();
});
```

This still works and is registered alongside the macros, so existing migrations
need no conversion. New migrations should prefer Laravel's own Schema and
Blueprint above; the subclass exists only for compatibility.

On Laravel 12 and 13 this style requires 1.2.0 or later, which widened the
blueprint resolver to accept the Blueprint constructor signature introduced in
Laravel 12.

## Version notes ##

Before 1.3.1 the two styles were mutually exclusive, chosen by a Laravel
version check: applications using one found the other unavailable. Since 1.3.1
both are always registered. If you are on 1.3.0 and your migrations type-hint
`Konsulting\Laravel\EditorStamps\Blueprint`, upgrade rather than converting
them.

## Tests ##

```
composer install
vendor/bin/phpunit
```

The suite uses Orchestra Testbench against an in-memory SQLite database, and
covers both migration styles, the columns they produce, and the trait.

## Contributing ##

Contributions are welcome and will be fully credited. We will accept contributions by Pull Request.

Please:

* Use the PSR-2 Coding Standard
* Document changes in behaviour, including readme.md
