<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class CdrUsageCostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'datetime' => dateFormat($this->datetime, 'Y-m-d H:i:s'),
            'caller' => $this->sender,
            'recipient' => $this->recipient,
            'duration' => dateFormat($this->duration, 'H:i:s'),
            'cost' => $this->total_cost
        ];
    }
}
