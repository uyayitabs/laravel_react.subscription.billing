<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NetworkOperator extends Model
{
    protected $fillable = [
        'network_id',
        'operator_id'
    ];

    public static $fields = [
        'network_id',
        'operator_id'
    ];

    /**
     * Belongs to Network::class
     *
     * @return BelongsTo
     */
    public function network()
    {
        return $this->belongsTo(Network::class);
    }

    /**
     * Belongs to Operator::class
     *
     * @return BelongsTo
     */
    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }
}
