<?php

namespace App\Traits;

use App\Models\City;
use Carbon\Carbon;

trait HasLineJsonDataBrightblueTrait
{
    public function hasLineJsonDataBrightblueTrait()
    {
        return $this->jsonDatas()->where('backend_api', 'brightblue')->first();
    }
}
