<?php

namespace App\Models;

class LogActivity extends BaseModel
{
    protected $hidden = ['updated_at'];

    /**
     * @var array
     */
    protected $fillable = [
        'message',
        'json_data',
        'tenant_id',
        'facility',
        'facility_id',
        'severity',
        'status',
        'username',
        'user_id',
        'url',
        'method',
        'ip',
        'agent',
        'related_entity_type',
        'related_entity_id',
        'hp_timestamp',
    ];

    public static $fields = [
        'id',
        'message',
        'json_data',
        'facility_id',
        'facility',
        'tenant_id',
        'severity',
        'status',
        'user_id',
        'username',
        'url',
        'method',
        'ip',
        'agent',
        'created_at',
        'related_entity_type',
        'related_entity_id',
        'hp_timestamp',
    ];

    public static $searchables = [
        'hp_timestamp',
        'status',
        'severity',
        'message',
        'username',
        'json_data'
    ];

    public static $sortables = [
        'id',
        'hp_timestamp',
        'status',
        'severity',
        'message',
        'username',
        'json_data'
    ];

    public static $filters = [
        'id',
        'hp_timestamp',
        'status',
        'severity',
        'message',
        'username',
        'json_data'
    ];

    public static $scopes = [
        'tenant'
    ];

    protected $casts = [
        'id' => 'int',
        'json_data' => 'array',
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->hp_timestamp = round(microtime(true) * 1000);
        });

        self::updating(function ($model) {
            $model->hp_timestamp = round(microtime(true) * 1000);
        });
    }

    /**
     * Get binding Tenant
     *
     * @return \Tenant
     */

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get binding facility
     *
     * @return \Facility
     */

    public function facilityType()
    {
        return $this->belongsTo(Facility::class, 'facility_id', 'id');
    }

    public function relatedEntity()
    {
        $relatedEntityModel =  null;
        switch ($this->related_entity_type) {
            case 'subscription':
                $relatedEntityModel = Subscription::class;
                break;

            case 'relation':
                $relatedEntityModel = Relation::class;
                break;

            case 'invoice':
                $relatedEntityModel = SalesInvoice::class;
                break;

            case 'product':
                $relatedEntityModel = Product::class;
                break;

            case 'billing_run':
                $relatedEntityModel = BillingRun::class;
                break;

            case 'queue_job':
                $relatedEntityModel = QueueJob::class;
                break;
        }
        return $relatedEntityModel::find($this->related_entity_id);
    }
}
