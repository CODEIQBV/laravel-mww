<?php

namespace YourNamespace\MyOnlineStore\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentMethodResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'name' => $this->resource['name'],
            'displayName' => $this->resource['displayName'],
        ];
    }
} 