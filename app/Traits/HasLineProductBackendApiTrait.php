<?php

namespace App\Traits;

use App\Models\City;

trait HasLineProductBackendApiTrait
{
    /**
     *
     * @param mixed $api
     * @return int
     */
    public function getInvoiceLineCountWithBackendApi($api): int
    {
        return $this->salesInvoiceLines()->whereHas("product", function ($query) use ($api) {
            $query->where("backend_api", $api);
        })->count();
    }
}
