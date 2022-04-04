<?php

namespace App\Repositories;

use App\Models\Subscription;

class SubscriptionRepository extends Repository
{
    // Constructor to bind model to repo
    public function __construct()
    {
        $this->model = Subscription::class;
    }
}
