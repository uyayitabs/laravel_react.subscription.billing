<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LogActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $hpTimestamp = Carbon::createFromTimestampMs($this->hp_timestamp);
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'date' => $hpTimestamp->copy()->format('Y-m-d'),
            'time' => $hpTimestamp->copy()->format('H:i:s.u'), //H:i:s.u
            'type' => $this->facilityType->description,
            'message' => $this->message,
            'details' => json_encode($this->json_data),
            'severity' => $this->severity,
            'username' => $this->username
        ];
    }
}
