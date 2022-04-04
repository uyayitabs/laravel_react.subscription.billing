<?php

namespace App\Repositories;

use App\Models\SubscriptionData;

class SubscriptionDataRepository extends Repository
{
    // Constructor to bind model to repo
    public function __construct()
    {
        $this->model = SubscriptionData::class;
    }

    public function getBy($where)
    {
        return $this->model::where($where)->first();
    }
}
