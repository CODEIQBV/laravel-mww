<?php

namespace YourNamespace\MyOnlineStore\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DiscountCodeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource['id'],
            'code' => $this->resource['code'],
            'description' => $this->resource['description'],
            'percentage_discount' => $this->resource['percentage_discount'],
            'fixed_discount' => $this->resource['fixed_discount'],
            'minimum_order_price' => $this->resource['minimum_order_price'],
            'minimum_products' => $this->resource['minimum_products'],
            'applies_to_shipping' => $this->resource['applies_to_shipping'],
            'free_shipping' => $this->resource['free_shipping'],
            'applies_to_action_prices' => $this->resource['applies_to_action_prices'],
            'single_use' => $this->resource['single_use'],
            'active' => $this->resource['active'],
            'valid_from' => $this->resource['valid_from'] ?? null,
            'valid_until' => $this->resource['valid_until'] ?? null,
            'valid_products' => ValidProductResource::collection($this->resource['valid_products'] ?? []),
            'customer_id' => $this->resource['customer_id'] ?? null,
        ];
    }
} 