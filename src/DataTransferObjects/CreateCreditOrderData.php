<?php

namespace YourNamespace\MyOnlineStore\DataTransferObjects;

class CreateCreditOrderData
{
    public function __construct(
        public readonly string $credited_order_number,
        public readonly int $status,
        public readonly bool $archived,
        public readonly ?array $comments,
        public readonly array $orderlines,
        public readonly ?string $shipping_costs = null,
        public readonly ?string $payment_costs = null,
        public readonly ?string $offline_location_id = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            credited_order_number: $data['credited_order_number'],
            status: $data['status'],
            archived: $data['archived'] ?? false,
            comments: $data['comments'] ?? null,
            orderlines: $data['orderlines'],
            shipping_costs: $data['shipping_costs'] ?? null,
            payment_costs: $data['payment_costs'] ?? null,
            offline_location_id: $data['offline_location_id'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'credited_order_number' => $this->credited_order_number,
            'status' => $this->status,
            'archived' => $this->archived,
            'comments' => $this->comments,
            'orderlines' => $this->orderlines,
            'shipping_costs' => $this->shipping_costs,
            'payment_costs' => $this->payment_costs,
            'offline_location_id' => $this->offline_location_id,
        ], fn($value) => !is_null($value));
    }
} 