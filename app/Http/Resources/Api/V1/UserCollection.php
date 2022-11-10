<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCollection extends ResourceCollection
{
    public function toArray($request)
    {

        return [
            'data' => $this->collection->map(function ($item) {
                return [
                    'ID' => $item->id,
                    'name' => $item->name,
                    'email' => $item->email,
                    'mobile_number' => $item->mobile_number,

                ];
            })
        ];
    }

    public function with($request)
    {
        return [
            'success' => 'عملیات شما با موفقیت انجام شد',
            'status' =>  200
        ];
    }
}
