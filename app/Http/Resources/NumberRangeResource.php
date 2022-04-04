<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\ApiResourceTrait;

class NumberRangeResource extends JsonResource
{
    use ApiResourceTrait;

    protected $message = '';
    protected $success;
    protected $list;

    public function __construct($resource, $message, $success, $list = false)
    {
        parent::__construct($resource);
        $this->message = $message;
        $this->success = $success;
        $this->list = $list;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if ($this->list) {
            return [
                'id' => $this->id,
                'tenant_id' => $this->tenant_id,
                'type' => $this->type,
                'description' => $this->description,
                'start' => $this->start,
                'end' => $this->end,
                'format' => $this->format,
                'randomized' => $this->randomized,
                'current' => $this->current,
                'sample_implementation' => $this->sample_implementation
            ];
        }

        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'type' => $this->type,
            'description' => $this->description,
            'start' => $this->start,
            'end' => $this->end,
            'format' => $this->format,
            'randomized' => $this->randomized,
            'current' => $this->current,
        ];
    }
}
