<?php

namespace YourNamespace\MyOnlineStore\DataTransferObjects;

class CreateArticleData
{
    public function __construct(
        public readonly string $name,
        public readonly string $description,
        public readonly string $sku,
        public readonly ?string $badge_text = null,
        public readonly bool $taxable = true,
        public readonly array $price = [],
        public readonly int $stock = 0,
        public readonly int $delivery_days = 0,
        public readonly ?string $meta_title = null,
        public readonly ?string $meta_description = null,
        public readonly bool $can_backorder = false,
        public readonly array $extra = [],
        public readonly array $categories = [],
        public readonly array $lists = [],
        public readonly array $images = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            description: $data['description'],
            sku: $data['sku'],
            badge_text: $data['badge_text'] ?? null,
            taxable: $data['taxable'] ?? true,
            price: $data['price'] ?? [],
            stock: $data['stock'] ?? 0,
            delivery_days: $data['delivery_days'] ?? 0,
            meta_title: $data['meta_title'] ?? null,
            meta_description: $data['meta_description'] ?? null,
            can_backorder: $data['can_backorder'] ?? false,
            extra: $data['extra'] ?? [],
            categories: $data['categories'] ?? [],
            lists: $data['lists'] ?? [],
            images: $data['images'] ?? [],
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'description' => $this->description,
            'sku' => $this->sku,
            'badge_text' => $this->badge_text,
            'taxable' => $this->taxable,
            'price' => $this->price,
            'stock' => $this->stock,
            'delivery_days' => $this->delivery_days,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'can_backorder' => $this->can_backorder,
            'extra' => $this->extra,
            'categories' => $this->categories,
            'lists' => $this->lists,
            'images' => $this->images,
        ], fn($value) => !is_null($value));
    }
} 