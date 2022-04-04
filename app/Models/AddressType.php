<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddressType extends BaseModel
{
    protected $fillable = [
        'type',
    ];

    public static $fields = [
        'id',
        'type'
    ];

    public static $scopes = [
        'address'
    ];

    /**
     * Get binding Address
     *
     * @return \Address
     */

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_type_id', 'id');
    }
}
