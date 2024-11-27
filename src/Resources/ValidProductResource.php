<?php

namespace YourNamespace\MyOnlineStore\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ValidProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'product_id' => $this->resource['product_id'],
            'max_quantity' => $this->resource['max_quantity'] ?? null,
        ];
    }
} 