<?php

namespace YourNamespace\MyOnlineStore\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderStatusResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'status' => $this->resource['status'],
            'description' => $this->resource['description'],
            'credit_order' => $this->resource['credit_order'],
        ];
    }
} 