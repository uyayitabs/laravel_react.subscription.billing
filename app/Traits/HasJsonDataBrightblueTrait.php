<?php

namespace App\Traits;

use App\Models\City;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait HasJsonDataBrightblueTrait
{
    public function getJsonDataBrightblueAttribute()
    {
        return $this->jsonDatas()->where('backend_api', 'brightblue')->first();
    }

    public function getSubscriptionLineBrightblueAttribute()
    {
        return $this->subscriptionLines()
            ->whereHas("product", function (Builder $query) {
                $query->where('backend_api', 'brightblue');
            })
            ->orderBy("id", "desc")
            ->first();
    }

    public function getBrightblueBasisProvisioningStatusAttribute()
    {
        $provisioned = $deprovisioned = false;
        $basisCount = 0;
        $productId = null;

        $lineQuery = $this->subscriptionLines()
            ->whereHas("product", function ($query) {
                $query->where("backend_api", "brightblue")
                    ->whereHas('jsonData', function ($jsonDataQuery) {
                        $jsonDataQuery->where("json_data->brightblue-fiber->type", 'basis');
                    });
            });
        $basisCount = $lineQuery->count();
        $subscriptionLine = $lineQuery->first();

        if ($basisCount && $subscriptionLine->jsonDatas()->count()) {
            $productId = $subscriptionLine->product_id;
            $jsonDataBasisLine = $subscriptionLine->jsonDatas()->first();
            $provisioned = Str::contains(
                $jsonDataBasisLine->json_data["brightblue"]["fiber-nl"]["provisioning"]["status"],
                [
                    "Provisioned",
                    "Suspended"
                ]
            );
            $deprovisioned = Str::contains(
                $jsonDataBasisLine->json_data["brightblue"]["fiber-nl"]["provisioning"]["status"],
                ["Deprovisioned"]
            );
        }

        return [
            "product_id" => $productId,
            "basis_count" => $basisCount,
            "provisioned" => $provisioned,
            "deprovisioned" => $deprovisioned
        ];
    }
}
