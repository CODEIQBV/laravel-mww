<?php

namespace YourNamespace\MyOnlineStore\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderLineResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource['id'],
            'type' => $this->resource['type'],
            'quantity' => $this->resource['quantity'],
            'weight' => $this->resource['weight'],
            'description' => $this->resource['description'],
            'price' => $this->when(
                !is_array($this->resource['price']), 
                fn() => new OrderLinePriceResource($this->resource['price']),
                fn() => OrderLinePriceResource::collection($this->resource['price'])
            ),
            'articles' => isset($this->resource['articles']) 
                ? OrderLineArticleResource::collection($this->resource['articles']) 
                : [],
        ];
    }
} 