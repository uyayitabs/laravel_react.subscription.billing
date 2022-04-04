<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\ApiResourceTrait;
use Carbon\Carbon;

class RelationPersonResource extends JsonResource
{
    use ApiResourceTrait;

    protected $message = '';
    protected $success;
    protected $list;

    private $relationsPerson;

    public function __construct($resource, $relationsPerson, $message = null, $success = null, $list = false)
    {
        parent::__construct($resource);
        $this->relationsPerson = $relationsPerson;
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
        $userObj = $this->user;
        $user = null;
        if ($userObj) {
            $userGroups = count($userObj->userGroups) > 0 ? $userObj->userGroups[0] : null;
            $groups = null;
            if ($userGroups) {
                $groups = [
                    'id' =>  $userGroups->group_id,
                    'name' => $userGroups->group->name,
                ];
            }
            $last_login = dateFormat($this->user->last_login);
            $user = [
                'id' => $this->user->id,
                'username' => $this->user->username,
                'email' => $this->user->email,
                'person_id' => $this->user->person_id,
                'last_tenant_id' => $this->user->last_tenant_id,
                'last_login' => $last_login,
                'enabled' => $userObj->enabled,
                'groups' => $groups,
            ];
        }

        $customer_number = $this->relationsPerson->relation->customer_number;

        if ($this->list) {
            return [
                'id' => $this->id,
                'person_type_id' => $this->relationsPerson->person_type_id,
                'status' => $this->relationsPerson->status,
                'gender' => $this->gender,
                'title' => $this->title,
                'first_name' => $this->first_name,
                'middle_name' => $this->middle_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'phone' => $this->phone,
                'mobile' => $this->mobile,
                'language' => $this->language,
                'linkedin' => $this->linkedin,
                'facebook' => $this->facebook,
                'birthdate' => dateFormat($this->birthdate),
                'primary' => $this->relationsPerson->primary,
                'customer_number' => $customer_number,
                'full_name' => $this->full_name,
                'user' => $user,
                'relation_type' => $this->relation_type
            ];
        }

        return [
            'id' => $this->id,
            'person_type_id' => $this->relationsPerson->person_type_id,
            'status' => $this->relationsPerson->status,
            'gender' => $this->gender,
            'title' => $this->title,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'mobile' => $this->mobile,
            'language' => $this->language,
            'linkedin' => $this->linkedin,
            'facebook' => $this->facebook,
            'birthdate' => dateFormat($this->birthdate),
            'primary' => $this->relationsPerson->primary,
            'customer_number' => $customer_number,

            // attributes
            'full_name' => $this->full_name,
            'removable' => $this->removable,

            // user
            'user' => $user,
            'relation_type' => $this->relation_type
        ];
    }
}
