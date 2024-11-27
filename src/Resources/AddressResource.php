<?php

namespace YourNamespace\MyOnlineStore\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'gender' => $this->resource['gender'],
            'name' => $this->resource['name'],
            'company' => $this->resource['company'] ?? null,
            'street' => $this->resource['street'],
            'number' => $this->resource['number'],
            'zipcode' => $this->resource['zipcode'],
            'city' => $this->resource['city'],
            'country' => $this->resource['country'],
            'country_code' => $this->resource['country_code'],
            'phone' => $this->resource['phone'],
            'bankaccount' => $this->resource['bankaccount'] ?? null,
        ];
    }
} 