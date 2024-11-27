<?php

namespace YourNamespace\MyOnlineStore\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentDetailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource['id'],
            'gateway' => $this->resource['gateway'],
            'method' => $this->resource['method'],
            'gatewayReference' => $this->resource['gatewayReference'],
            'price' => $this->resource['price'],
            'currency' => $this->resource['currency'],
            'storeId' => $this->resource['storeId'],
            'reference' => $this->resource['reference'],
            'isTest' => $this->resource['isTest'],
            'createdAt' => $this->resource['createdAt'],
            'updatedAt' => $this->resource['updatedAt'],
            'mutations' => isset($this->resource['mutations']) 
                ? PaymentMutationResource::collection($this->resource['mutations']) 
                : null,
            'order' => isset($this->resource['order']) 
                ? new PaymentOrderResource($this->resource['order']) 
                : null,
            'properties' => $this->resource['properties'] ?? null,
        ];
    }
} 