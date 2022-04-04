<?php

namespace App\Repositories;

use App\Models\Provider;

class ProviderRepository extends Repository
{
    // Constructor to bind model to repo
    public function __construct()
    {
        $this->model = Provider::class;
    }

    public function validateProvider($provider, $token, $ip)
    {
        return $this->model::validateProvider($provider, $token, $ip)->first();
    }
}
