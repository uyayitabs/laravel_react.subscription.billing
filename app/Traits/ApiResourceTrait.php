<?php

namespace App\Traits;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

trait ApiResourceTrait
{
    /**
     * Get any additional data that should be returned with the resource array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function with($request)
    {
        return [
            'status' => $this->status,
            'message' => $this->message,
        ];
    }

    /**
     * Add additional meta data to the resource response.
     *
     * @param  array  $data
     * @return $this
     */
    public static function collection($resource)
    {
        return [
            'status' => '',
            'message' => '',
            'data' => new AnonymousResourceCollection($resource, static::class),
        ];
    }
}
