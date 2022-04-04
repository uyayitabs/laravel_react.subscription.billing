<?php

namespace App\Traits;

use App\Models\City;
use Carbon\Carbon;
use Illuminate\Support\Str;

trait HasJsonDataM7Trait
{
    public function getJsonDataM7Attribute()
    {
        return $this->jsonDatas()->where('backend_api', 'm7')->first();
    }

    public function getM7StbLinesAttribute()
    {
        return $this->subscriptionLines()
            ->whereHas('product', function ($query) {
                $query->whereHas('jsonData', function ($q1) {
                    $q1->where('json_data->m7->type', 'stb');
                });
            })
            ->orderBy('id')
            ->get();
    }

    public function getM7ProvisioningStbLinesAttribute()
    {
        return $this->subscriptionLines()
            ->whereHas('product', function ($query) {
                $query->whereHas('jsonData', function ($q1) {
                    $q1->where('json_data->m7->type', 'stb');
                });
            })
            ->where(function ($query) {
                $query->whereNull('subscription_stop')
                    ->orWhere('subscription_stop', '>', Carbon::now()->format('Y-m-d'));
            })
            ->orderBy('id')
            ->get();
    }

    public function getIsM7Attribute()
    {
        return $this->subscriptionLines()->whereHas('product', function ($query) {
            $query->whereHas('jsonData', function ($q1) {
                $q1->where('json_data->m7->type', 'stb');
            });
        })->exists();
    }

    public function getM7ProductLinesAttribute()
    {
        return $this->subscriptionLines()->whereHas('product', function ($query) {
            $query->whereHas('jsonData', function ($q1) {
                $q1->where('json_data->m7->type', '<>', 'stb');
            });
        })->get();
    }

    public function getM7ProvisioningProductLinesAttribute()
    {
        return $this->subscriptionLines()->whereHas('product', function ($query) {
            $query->whereHas('jsonData', function ($q1) {
                $q1->where('json_data->m7->type', '<>', 'stb');
            });
        })
            ->whereRaw('subscription_stop is null or subscription_stop < ' . now()->format('Y-m-d'))
            ->get();
    }

    public function getM7ProvisionedAttribute()
    {
        $subscriptionJsonData = $this->json_data_m7;
        $isM7 = $subscriptionJsonData && array_key_exists('m7', $subscriptionJsonData->json_data);
        $status = $isM7 && array_key_exists(
            'status',
            $subscriptionJsonData->json_data['m7']
        ) ? $subscriptionJsonData->json_data['m7']['status'] : null;

        $isProvisioned = $subscriptionJsonData &&
            array_key_exists('m7', $subscriptionJsonData->json_data) &&
            array_key_exists('status', $subscriptionJsonData->json_data['m7']) &&
            'Provisioned' == $subscriptionJsonData->json_data['m7']['status'];

        return $isProvisioned;
    }

    public function getContractNumberAttribute()
    {
        $jsonData = $this->json_data_m7;
        $contractNumber = $this->id;
        if (
            $jsonData &&
            isset($jsonData->json_data['m7']) &&
            isset($jsonData->json_data['m7']['transaction']) &&
            $jsonData->json_data['m7']['transaction'] == 'migration' &&
            (!isset($jsonData->json_data['m7']['status']) ||
                strtolower($jsonData->json_data['m7']['status']) != 'provisioned') &&
            isset($jsonData->json_data['m7']['name'])
        ) {
            $contractNumber .= '$' . $jsonData->json_data['m7']['name'];
        }

        return $contractNumber;
    }

    public function getMainStbLineAttribute()
    {
        $main_stb_line = null;
        $m7_stb_lines = $this->m7_stb_lines;
        if ($this->m7_provisioned) {
            $main_stb_line =  $this->subscriptionLines()
                ->whereHas('product', function ($query) {
                    $query->whereHas('jsonData', function ($q1) {
                        $q1->where('json_data->m7->type', 'stb');
                    });
                })
                ->whereHas('jsonDatas', function ($query) {
                    $query->where('json_data->m7->type', 'main');
                })
                ->first();

            if ($main_stb_line) {
                return $main_stb_line;
            }

            if (!$main_stb_line) {
                foreach ($m7_stb_lines as $stb_line) {
                    if ('Provisioned' == $stb_line->m7_provisioning_status) {
                        $main_stb_line = $stb_line;
                        break;
                    }
                }
            }

            if ($main_stb_line) {
                return $main_stb_line;
            }
        }

        if (!$main_stb_line) {
            foreach ($m7_stb_lines as $stb_line) {
                if ($stb_line->serial && null == $stb_line->subscription_stop) {
                    $main_stb_line = $stb_line;
                    break;
                }
            }
        }

        if ($main_stb_line) {
            return $main_stb_line;
        }

        $main_stb_line =  $this->subscriptionLines()->where(function ($query) {
            $query->whereHas('product', function ($q1) {
                $q1->whereHas('jsonData', function ($q2) {
                    $q2->where('json_data->m7->type', 'stb');
                });
            })->whereNotNull('serial');
        })->orderBy('subscription_stop', 'DESC')->first();

        return $main_stb_line;
    }

    public function getMacAddressesAttribute()
    {
        $mac_addresses = [];
        foreach ($this->m7_stb_lines as $subscriptionLine) {
            $serial = $subscriptionLine->serial_item;
            if ($serial) {
                $mac_addresses[] = $serial->json_data['serial']['mac'];
            }
        }

        return $mac_addresses;
    }

    public function getMainMacAddressAttribute()
    {
        $mainLine = $this->main_stb_line;
        return $mainLine ? $mainLine->mac_address : null;
    }

    public function getCustomerNumberAttribute()
    {
        $jsonData = $this->json_data_m7;
        return $jsonData &&
            isset($jsonData->json_data['m7']) &&
            isset($jsonData->json_data['m7']['CustomerNumber']) ?
            $jsonData->json_data['m7']['CustomerNumber'] : '';
    }

    public function getM7HasProvisioningAttribute()
    {
        $stbLines = $this->m7_stb_lines;
        $productLines = $this->m7_product_lines;
        $hasNewStb = false;
        $hasNewProd = false;
        $hasNewBasis = false;
        foreach ($stbLines as $stbLine) {
            if (
                $stbLine->is_started &&
                !$stbLine->json_data_m7 &&
                !$stbLine->is_stoped &&
                $stbLine->is_completed
            ) {
                $hasNewStb = true;
                break;
            }
        }
        foreach ($productLines as $productLine) {
            if (
                $productLine->is_started &&
                !$productLine->json_data_m7 &&
                !$productLine->is_stoped &&
                $productLine->is_completed
            ) {
                if (!$hasNewProd) {
                    $hasNewProd = true;
                }
                if ($productLine->json_data_product_type == 'basis') {
                    $hasNewBasis = true;
                }

                if ($hasNewProd && $hasNewBasis) {
                    break;
                }
            }
        }
        return [
            'hasNewStb' => $hasNewStb,
            'hasNewProd' => $hasNewProd,
            'hasNewBasis' => $hasNewBasis
        ];
    }

    public function getM7HasDeprovisioningAttribute()
    {
        $stbLines = $this->m7_stb_lines;
        $productLines = $this->m7_product_lines;
        $hasDeProStb = false;
        $hasDeProStbMain = false;
        $hasDeProProd = false;
        $hasDeProBas = false;
        $main_mac_address = $this->main_mac_address;

        foreach ($stbLines as $stbLine) {
            if (!$hasDeProStb) {
                $hasDeProStb = $stbLine->is_stoped &&
                    'Provisioned' == $stbLine->m7_provisioning_status;
            }
            if (
                $stbLine->is_stoped &&
                'Provisioned' == $stbLine->m7_provisioning_status &&
                strtolower($main_mac_address) == $stbLine->mac_address
            ) {
                $hasDeProStbMain = true;
            }
            if ($hasDeProStb && $hasDeProStbMain) {
                break;
            }
        }
        foreach ($productLines as $productLine) {
            if (
                $productLine->is_stoped &&
                'Provisioned' == $productLine->m7_provisioning_status
            ) {
                if (!$hasDeProProd) {
                    $hasDeProProd = true;
                }
                if ($productLine->json_data_product_type == 'basis') {
                    $hasDeProBas = true;
                }
                if ($hasDeProProd && $hasDeProBas) {
                    break;
                }
            }
        }
        return [
            'hasDeProStb' => $hasDeProStb,
            'hasDeProProd' => $hasDeProProd,
            'hasDeProBas' => $hasDeProBas,
            'hasDeProStbMain' => $hasDeProStbMain
        ];
    }

    /**
     * Get M7 Provisioning products
     *
     * @return boolean
     */
    public function getM7IsForProvisioningAttribute()
    {
        $count = $this->subscriptionLines()
            ->with('jsonData')
            ->whereHas('jsonData', function ($query) {
                $query->whereNotNull('transaction_id');
            })->count();
        return $count == 0;
    }

    /**
     * Get M7 Provisioning products
     *
     * @return boolean
     */
    public function getM7IsForDeProvisionAttribute()
    {
        if ($this->subscription_stop != null) {
            $count = $this->whereHas('jsonData', function ($query) {
                $query->where('json_data->CustomerNumber', '<>', '');
            })->count();
            return $count == 0;
        }
    }

    /**
     * M7 Provisionable check
     *
     * @return array
     */
    public function getM7ProvisionableAttribute()
    {
        $hasStb = false;
        $hasBasis = false;
        $basicCount = 0;
        $invalidProdIds = 0;
        $prodIds = [];
        $completeSerial = false;
        $stbCount = 0;
        foreach ($this->provider_lines as $subscriptionLine) {
            if (!$subscriptionLine->is_started) {
                continue;
            }
            if (
                $subscriptionLine->subscription_stop == null ||
                $subscriptionLine->subscription_stop->format('Y-m-d') > now()->format('Y-m-d')
            ) {
                $product = $subscriptionLine->product;
                $jsonData = $product->jsonData;

                if ($jsonData) {
                    $ptype = $jsonData->json_data['m7']['type'];

                    switch ($ptype) {
                        case 'stb':
                            $stbCount++;
                            $hasStb = true;
                            if ($subscriptionLine->serial) {
                                $completeSerial = true;
                            }
                            break;

                        case 'basis':
                            $hasBasis = true;
                            $basicCount++;
                            break;

                        case 'addon':
                            $productId = $jsonData->json_data['m7']['productId'];
                            if (!$invalidProdIds) {
                                $invalidProdIds = in_array($productId, $prodIds);
                            }
                            $prodIds[] = $jsonData->json_data['m7']['productId'];
                            break;
                    }
                }
            }
        }

        return [
            'hasStb' => $hasStb,
            'hasBasis' => $hasBasis,
            'basicCount' => $basicCount,
            'invalidProdIds' => $invalidProdIds,
            'completeSerial' => $completeSerial,
            'stbCount' => $stbCount
        ];
    }

    /**
     * Get Provider Subscription Lines Attribute
     *
     * @return array
     */
    public function getProviderLinesAttribute()
    {
        $subscriptionLines = $this->subscriptionLines()
            ->whereHas('product', function ($query) {
                $query->whereNotNull('backend_api');
                $query->where('backend_api', 'm7');
            })
            ->orderBy('id')
            ->get();
        return $subscriptionLines;
    }

    /**
     * Get Provider products
     *
     * @return array
     */
    public function getProviderProductsAttribute()
    {
        $products = [];
        $productsStb = [];
        foreach ($this->provider_lines as $subscriptionLine) {
            if (
                $subscriptionLine->subscription_start == null ||
                $subscriptionLine->subscription_start <= now()
            ) {
                if (
                    $subscriptionLine->subscription_stop == null ||
                    $subscriptionLine->subscription_stop > now()
                ) {
                    $product = $subscriptionLine->product()->with('jsonData')->first();
                    $jsonData = $product->jsonData;

                    if ($jsonData) {
                        if ($jsonData->json_data['m7']['type'] == 'stb') {
                            $productsStb[] = [
                                'subscriptionLine' => $subscriptionLine,
                                'product' => $product
                            ];
                        } else {
                            $products[] = [
                                'subscriptionLine' => $subscriptionLine,
                                'product' => $product
                            ];
                        }
                    }
                }
            }
        }

        return [
            'products' => $products,
            'productsStb' => $productsStb
        ];
    }

    /**
     * Get m7 provisioning status
     * @return string
     */
    public function getM7ProvisioningStatusAttribute()
    {
        $jsonData = $this->json_data_m7;
        if (!$jsonData) {
            return 'Provisioning';
        }
        if (
            array_key_exists('m7', $jsonData->json_data) &&
            array_key_exists('status', $jsonData->json_data['m7'])
        ) {
            return $jsonData->json_data['m7']['status'];
        }
        return 'Provisioned';
    }

    public function getM7BasisProvisioningStatusAttribute()
    {
        $provisioned = $deprovisioned = false;
        $basisCount = 0;
        $productId = null;

        $lineQuery = $this->subscriptionLines()
            ->whereHas("product", function ($query) {
                $query->where("backend_api", "m7")
                    ->whereHas('jsonData', function ($jsonDataQuery) {
                        $jsonDataQuery->where("json_data->m7->type", 'basis');
                    });
            });
        $basisCount = $lineQuery->count();
        $subscriptionLine = $lineQuery->first();

        if ($basisCount && $subscriptionLine->jsonDatas()->count()) {
            $productId = $subscriptionLine->product_id;
            $jsonDataBasisLine = $subscriptionLine->jsonDatas()->first();
            $provisioned = Str::contains(
                $jsonDataBasisLine->json_data["m7"]["status"],
                [
                    "Provisioned",
                    "Suspended"
                ]
            );
            $deprovisioned = Str::contains(
                $jsonDataBasisLine->json_data["m7"]["status"],
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
