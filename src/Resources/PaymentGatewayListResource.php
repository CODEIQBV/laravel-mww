<?php

namespace YourNamespace\MyOnlineStore\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentGatewayListResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'gateways' => PaymentGatewayResource::collection($this->resource['gateways']),
        ];
    }
} 