<?php

namespace Konsulting\Laravel\EditorStamps;

use Illuminate\Support\Facades\Auth;

trait EditorStamps
{
    public static function bootEditorStamps()
    {
        static::creating(function ($model) {
            if (! Auth::check()) {
                return;
            }

            $userId = Auth::user()->id;

            $model->created_by = $userId;
            $model->updated_by = $userId;
        });

        static::updating(function ($model) {
            if (! Auth::check()) {
                return;
            }

            $model->updated_by = Auth::user()->id;
        });

        static::deleting(function ($model) {
            if (! Auth::check()) {
                return;
            }

            $model->updated_by = Auth::user()->id;
        });
    }

    public function creator()
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'updated_by');
    }
}
