<?php

namespace YourNamespace\MyOnlineStore\DataTransferObjects;

class CreateDiscountCodeData
{
    public function __construct(
        public readonly string $description,
        public readonly ?string $percentage_discount = null,
        public readonly ?string $fixed_discount = null,
        public readonly ?string $minimum_order_price = null,
        public readonly ?int $minimum_products = null,
        public readonly bool $applies_to_shipping = false,
        public readonly bool $free_shipping = false,
        public readonly bool $applies_to_action_prices = false,
        public readonly bool $single_use = false,
        public readonly bool $active = true,
        public readonly ?string $valid_from = null,
        public readonly ?string $valid_until = null,
        public readonly ?array $valid_products = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            description: $data['description'],
            percentage_discount: $data['percentage_discount'] ?? null,
            fixed_discount: $data['fixed_discount'] ?? null,
            minimum_order_price: $data['minimum_order_price'] ?? null,
            minimum_products: $data['minimum_products'] ?? null,
            applies_to_shipping: $data['applies_to_shipping'] ?? false,
            free_shipping: $data['free_shipping'] ?? false,
            applies_to_action_prices: $data['applies_to_action_prices'] ?? false,
            single_use: $data['single_use'] ?? false,
            active: $data['active'] ?? true,
            valid_from: $data['valid_from'] ?? null,
            valid_until: $data['valid_until'] ?? null,
            valid_products: $data['valid_products'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'description' => $this->description,
            'percentage_discount' => $this->percentage_discount,
            'fixed_discount' => $this->fixed_discount,
            'minimum_order_price' => $this->minimum_order_price,
            'minimum_products' => $this->minimum_products,
            'applies_to_shipping' => $this->applies_to_shipping,
            'free_shipping' => $this->free_shipping,
            'applies_to_action_prices' => $this->applies_to_action_prices,
            'single_use' => $this->single_use,
            'active' => $this->active,
            'valid_from' => $this->valid_from,
            'valid_until' => $this->valid_until,
            'valid_products' => $this->valid_products,
        ], fn($value) => !is_null($value));
    }
} 