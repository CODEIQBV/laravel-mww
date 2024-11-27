<?php

namespace YourNamespace\MyOnlineStore\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'number' => $this->resource['number'],
            'uuid' => $this->resource['uuid'],
            'date' => $this->resource['date'],
            'time' => $this->resource['time'],
            'status' => $this->resource['status'],
            'archived' => $this->resource['archived'],
            'finished' => $this->resource['finished'],
            'taxed' => $this->resource['taxed'],
            'locale' => $this->resource['locale'],
            'discountcode' => $this->resource['discountcode'] ?? null,
            'comments' => $this->resource['comments'] ?? [],
            'business_model' => $this->resource['business_model'],
            'reference' => $this->resource['reference'],
            'offline_location_id' => $this->resource['offline_location_id'] ?? null,
            'currency' => $this->resource['currency'],
            'credited_order_number' => $this->resource['credited_order_number'] ?? null,
            'credit_order_numbers' => $this->resource['credit_order_numbers'] ?? [],
            'payment' => new PaymentResource($this->resource['payment']),
            'payment_status' => $this->resource['payment_status'],
            'debtor' => new DebtorResource($this->resource['debtor']),
            'price' => new PriceResource($this->resource['price']),
            'orderlines' => OrderLineResource::collection($this->resource['orderlines']),
            'shipping' => ShippingResource::collection($this->resource['shipping']),
        ];
    }
} 