<?php

namespace YourNamespace\MyOnlineStore\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CreatePaymentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'payment' => new PaymentDetailResource($this->resource['payment']),
            'url' => $this->resource['url'] ?? null,
        ];
    }
} 