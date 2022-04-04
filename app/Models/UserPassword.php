<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPassword extends Model
{
    protected $fillable = [
        'user_id',
        'password'
    ];

    public static function boot()
    {
        parent::boot();
        static::created(function ($model) {
            $count = UserPassword::where('user_id', $model->user_id)->count();
            if ($count > 10) {
                // Delete the oldest
                $last = UserPassword::where('user_id', $model->user_id)->orderBy('created_at', 'asc')->first();
                $last->delete();
            }
        });
    }
}
