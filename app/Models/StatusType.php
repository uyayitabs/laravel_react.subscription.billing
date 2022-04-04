<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusType extends BaseModel
{
    protected $fillable = [
        'id',
        'type',
    ];

    protected $appends = [];

    public static $fields = [
        'id',
        'type',
    ];

    public static $includes = [];

    public static $sorts = [
        'id',
        'type',
    ];

    public static $scopes = [
        'id',
        'type',
    ];

    /**
     * Get statuses
     *
     * @return \Status
     */
    public function statuses()
    {
        return $this->hasMany(Status::class);
    }
}
