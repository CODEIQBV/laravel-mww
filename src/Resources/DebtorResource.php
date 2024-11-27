<?php

namespace YourNamespace\MyOnlineStore\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DebtorResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'email' => $this->resource['email'],
            'address' => [
                'invoice' => new AddressResource($this->resource['address']['invoice']),
            ],
        ];
    }
} 