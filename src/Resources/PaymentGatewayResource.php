<?php

namespace YourNamespace\MyOnlineStore\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentGatewayResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'name' => $this->resource['name'],
            'displayName' => $this->resource['displayName'],
            'websiteUrl' => $this->resource['websiteUrl'],
            'methods' => PaymentMethodResource::collection($this->resource['methods']),
        ];
    }
} 