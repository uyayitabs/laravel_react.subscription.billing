<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUserTrait;
use Carbon\Carbon;

class UserCode extends BaseModel
{
    use HasUserTrait;

    protected $fillable = [
        'user_id',
        'code',
        'expiration'
    ];

    protected static function boot()
    {
        parent::boot();

        // auto-sets values on creation
        static::creating(function ($query) {
            $query->expiration = now()->addHours(2);
            $query->code = \Str::random(64);
        });
    }

    public function scopeActive($query)
    {
        $query->where('expiration', '>=', now());
    }

    public function getIsExpiredAttribute()
    {
        return Carbon::parse($this->expiration) < now();
    }
}
