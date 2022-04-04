<?php

namespace App\Models;

use App\Traits\HasCompositePrimaryKeyTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductHierarchy extends BaseModel
{
    protected $table = 'product_hierarchies';

    protected $fillable = [
        'product_id',
        'related_product_id',
        'relation_type',
        'json_data',
    ];

    public static $fields = [
        'product_id',
        'related_product_id',
        'relation_type',
        'json_data',
    ];

    protected $casts = [
        'json_data' => 'array'
    ];

    protected $primaryKey = ['product_id', 'related_product_id'];
    public $incrementing = false;


    /**
     * Product relationship
     *
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    /**
     * Related Product relationship
     *
     * @return BelongsTo
     */
    public function relatedProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'related_product_id', 'id');
    }

    public function relationType(): HasOne
    {
        return $this->hasOne(ProductHierarchyRelationType::class, 'id', 'relation_type');
    }

    /**
     * Override Illuminate\Database\Eloquent\Model::setKeysForSaveQuery()
     * Because [product_hierarchies] table does not have (id) column, instead has 2 primary (keys) columns (product_id, related_product_id)
     * Need to override to fix issue on create(), update() and delete() methods
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setKeysForSaveQuery($query)
    {
        $keys = $this->getKeyName();
        if (!is_array($keys)) {
            return parent::setKeysForSaveQuery($query);
        }

        foreach ($keys as $keyName) {
            $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
        }

        return $query;
    }

    /**
     * Override Illuminate\Database\Eloquent\Model::getKeyForSaveQuery()
     * Because [product_hierarchies] table does not have (id) column, instead has 2 primary (keys) columns (product_id, related_product_id)
     * Need to override to fix issue on create(), update() and delete() methods
     *
     * @param mixed $keyName
     * @return mixed
     */
    protected function getKeyForSaveQuery($keyName = null)
    {
        if (is_null($keyName)) {
            $keyName = $this->getKeyName();
        }

        return $this->original[$keyName] ?? $this->getAttribute($keyName);
    }
}
