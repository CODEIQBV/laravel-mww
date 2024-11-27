<?php

namespace YourNamespace\MyOnlineStore\DataTransferObjects;

class UpdateArticleData
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $description = null,
        public readonly ?string $sku = null,
        public readonly ?string $badge_text = null,
        public readonly ?bool $taxable = null,
        public readonly ?array $price = null,
        public readonly ?int $stock = null,
        public readonly ?int $delivery_days = null,
        public readonly ?string $meta_title = null,
        public readonly ?string $meta_description = null,
        public readonly ?bool $can_backorder = null,
        public readonly ?array $extra = null,
        public readonly ?array $categories = null,
        public readonly ?array $lists = null,
        public readonly ?array $images = null,
        public readonly ?array $variants = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            description: $data['description'] ?? null,
            sku: $data['sku'] ?? null,
            badge_text: $data['badge_text'] ?? null,
            taxable: $data['taxable'] ?? null,
            price: $data['price'] ?? null,
            stock: $data['stock'] ?? null,
            delivery_days: $data['delivery_days'] ?? null,
            meta_title: $data['meta_title'] ?? null,
            meta_description: $data['meta_description'] ?? null,
            can_backorder: $data['can_backorder'] ?? null,
            extra: $data['extra'] ?? null,
            categories: $data['categories'] ?? null,
            lists: $data['lists'] ?? null,
            images: $data['images'] ?? null,
            variants: $data['variants'] ?? null,
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
            'variants' => $this->variants,
        ], fn($value) => !is_null($value));
    }
} 