<?php

namespace YourNamespace\MyOnlineStore\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentListResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'payments' => PaymentDetailResource::collection($this->resource['payments']),
        ];
    }
} 