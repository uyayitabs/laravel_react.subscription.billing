<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductHierarchyRelationType extends BaseModel
{
    protected $table = 'product_hierarchies_relation_types';

    protected $fillable = [
        'type',
        'description',
    ];

    public static $fields = [
        'type',
        'description',
    ];
}
