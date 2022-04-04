<?php

namespace App\Http\Requests;
class NoteApiRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'type' => $this->requiredOrNullable . '|string',
            'related_id' => $this->requiredOrNullable . '|integer',
            'text' => $this->requiredOrNullable . '|string'
        ];
    }
}
