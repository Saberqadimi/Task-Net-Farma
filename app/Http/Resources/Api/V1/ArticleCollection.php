<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ArticleCollection extends ResourceCollection
{
    public function toArray($request)
    {

        return [
            'data' => $this->collection->map(function ($item) {
                return [
                    'ID' => $item->id,
                    'title' => $item->title,
                    'user_id' => $item->user->id,
                    'user_name' => $item->user->name,
                    'description' => $item->description,
                    'image' => $item->image
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
