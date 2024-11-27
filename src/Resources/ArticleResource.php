<?php

namespace YourNamespace\MyOnlineStore\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource['id'],
            'uuid' => $this->resource['uuid'],
            'name' => $this->resource['name'],
            'description' => $this->resource['description'],
            'sku' => $this->resource['sku'],
            'badge_text' => $this->resource['badge_text'] ?? null,
            'taxable' => $this->resource['taxable'],
            'price' => new ArticlePriceResource($this->resource['price']),
            'created_date' => $this->resource['created_date'],
            'created_time' => $this->resource['created_time'],
            'updated_date' => $this->resource['updated_date'],
            'updated_time' => $this->resource['updated_time'],
            'stock' => $this->resource['stock'],
            'delivery_days' => $this->resource['delivery_days'],
            'meta_title' => $this->resource['meta_title'] ?? null,
            'meta_description' => $this->resource['meta_description'] ?? null,
            'can_backorder' => $this->resource['can_backorder'],
            'extra' => $this->resource['extra'] ?? [],
            'url' => $this->resource['url'],
            'categories' => ArticleCategoryResource::collection($this->resource['categories']),
            'lists' => ArticleListResource::collection($this->resource['lists'] ?? []),
            'variants' => ArticleVariantResource::collection($this->resource['variants'] ?? []),
            'images' => ArticleImageResource::collection($this->resource['images'] ?? []),
        ];
    }
} 