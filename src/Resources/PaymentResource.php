<?php

namespace YourNamespace\MyOnlineStore\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'gateway_name' => $this->resource['gateway_name'],
            'method_name' => $this->resource['method_name'],
        ];
    }
} 