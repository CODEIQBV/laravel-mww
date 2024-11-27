<?php

namespace YourNamespace\MyOnlineStore\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShippingMethodResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'display_name' => $this->resource['display name'],
            'countries' => $this->resource['countries'],
            'default' => $this->resource['default'],
            'no_costs_above' => $this->resource['no_costs_above'],
        ];
    }
} 