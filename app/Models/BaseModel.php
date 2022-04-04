<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Traits\HasSearchTrait;

// use Akaunting\Money\Money;

class BaseModel extends Model
{
    use HasSearchTrait;

    public static $fields = [];
    public static $filters = [];
    public static $sortables = [];
    public static $searchables = [];
    public static $hiders = [];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * Scopes to return specific relationships
     * @param $query
     *
     * @return object|array data related models
     */
    public function scopeWithRelations($query, $relations = [])
    {
        return $query->with($relations);
    }

    /**
     * Get raw DB result without attributes/mutators
     */
    public function getRawDBData()
    {
        return DB::table($this->getTable())
            ->where('id', $this->id)
            ->first();
    }

    public function gadgetMenu($label, $action, $content)
    {
        return [
            'label' => $label,
            'action' => $action,
            'data' => $content
        ];
    }

    public static function getFilters()
    {
        return self::$filters;
    }

    public static function getSortables()
    {
        return self::$sortables;
    }

    public static function getFields()
    {
        return self::$fields;
    }

    public static function getSearchables()
    {
        return self::$searchables;
    }
}
