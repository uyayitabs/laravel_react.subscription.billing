<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class NoteResource extends ResourceCollection
{
    public function toArray($request)
    {
        $data = $this->collection->map(function ($note) {
            $full_name = $note->user ? $note->user->person->full_name : 'System';
            Arr::pull($note, 'user');
            $note['user_fullname'] = $full_name;
            return $note;
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
