<?php

namespace YourNamespace\MyOnlineStore\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OfflineLocationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource['id'],
            'name' => $this->resource['name'],
            'street' => $this->resource['street'],
            'street_number' => $this->resource['street_number'],
            'zipcode' => $this->resource['zipcode'],
            'city' => $this->resource['city'],
            'country_code' => $this->resource['country_code'],
            'country' => $this->resource['country'],
            'phone' => $this->resource['phone'],
            'email' => $this->resource['email'],
            'note' => $this->resource['note'] ?? null,
            'deleted' => $this->resource['deleted'],
        ];
    }
} 