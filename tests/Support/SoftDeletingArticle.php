<?php

namespace Konsulting\Laravel\EditorStamps\Tests\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Konsulting\Laravel\EditorStamps\EditorStamps;

class SoftDeletingArticle extends Model
{
    use EditorStamps, SoftDeletes;

    protected $table = 'soft_articles';

    protected $guarded = [];

    public $timestamps = false;
}
