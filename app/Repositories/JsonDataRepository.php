<?php

namespace App\Repositories;

use App\Models\JsonData;

class JsonDataRepository extends Repository
{
    // Constructor to bind model to repo
    public function __construct()
    {
        $this->model = JsonData::class;
    }

    public function getBy($where)
    {
        return $this->model::where($where);
    }
}
