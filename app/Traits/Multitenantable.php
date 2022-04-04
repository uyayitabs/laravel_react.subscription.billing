<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Multitenantable
{
    public static function bootMultitenantable()
    {
        static::addGlobalScope('tenant_id', function (Builder $builder) {
            if (auth()->check()) {
                return $builder->where('tenant_id', auth()->user()->tenant_id);
            }
        });
    }
}
