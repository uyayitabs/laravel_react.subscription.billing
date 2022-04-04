<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class VatCodeResource extends ResourceCollection
{
    public function toArray($request)
    {
        $data = $this->collection->map(function ($vat_code) {
            $vat_code['removable'] = !$vat_code->in_use;
            $vat_code['in_use'] = $vat_code->in_use;
            $vat_code['in_use_label'] = $vat_code->in_use ? 'In use' : 'Not used';
            $vat_code['account'] = $vat_code->account;
            $vat_code['vat_percentage'] = $vat_code->vat_percentage * 100;
            return $vat_code;
        });

        return [
            'data' => $data,
            'total' => $this->total()
        ];
    }

    public function toResponse($request)
    {
        return JsonResource::toResponse($request);
    }
}
