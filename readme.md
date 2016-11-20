# Editor Stamps #

This package provides a simple trait for your [Laravel](http://laravel.com) models which saves the creating/updating user when a model is saved.

It also includes a replacement Schema Facade and BluePrint for use in your migrations to make the addition of `created_by` and `updated_by` columns. 

## Usage ##

### To use the trait ###
Simply `use` it in your model. `use Konsulting\Laravel\EditorStamps\EditorStamps;`

### To use the Schema and Facade ###

Replace Schema with this packages in `config/app.php`.

`'Schema' => Illuminate\Support\Facades\Schema::class,`

to 

`'Schema' => \Klever\Laravel\EditorStamps\Schema::class,`


Then replace the Schema and Blueprint references in Migrations where you want to use them. ```php

use Klever\Laravel\EditorStamps\Schema;
use Klever\Laravel\EditorStamps\Blueprint;
```

## Contributing ##

Contributions are welcome and will be fully credited. We will accept contributions by Pull Request.

Please:
* Use the PSR-2 Coding Standard
* Document changes in behaviour, including readme.md.