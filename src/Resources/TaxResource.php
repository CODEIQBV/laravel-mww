<?php

namespace YourNamespace\MyOnlineStore\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaxResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'amount' => $this->resource['amount'],
            'rate' => $this->resource['rate'],
        ];
    }
} 