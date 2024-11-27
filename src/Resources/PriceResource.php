<?php

namespace YourNamespace\MyOnlineStore\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PriceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'tax' => TaxResource::collection($this->resource['tax']),
            'total' => $this->resource['total'],
        ];
    }
} 