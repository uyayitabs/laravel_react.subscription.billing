<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\ApiResourceTrait;

class UserResource extends JsonResource
{
    use ApiResourceTrait;

    public function __construct($resource)
    {
        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'person_id' => $this->person->id,
            'type' => $this->type,
            'last_login' => dateFormat($this->last_loging, 'Y-m-d H:i:s'),
            'enabled' => $this->enabled,
            'password_expiration' => dateFormat($this->password_expiration),
            'last_tenant_id' => $this->last_tenant_id,
            'full_name' => $this->person->full_name
        ];
    }
}
