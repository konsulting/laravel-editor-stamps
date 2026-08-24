<?php

namespace Konsulting\Laravel\EditorStamps\Tests\Support;

use Illuminate\Database\Eloquent\Model;
use Konsulting\Laravel\EditorStamps\EditorStamps;

class Article extends Model
{
    use EditorStamps;

    protected $table = 'articles';

    protected $guarded = [];

    public $timestamps = false;
}
