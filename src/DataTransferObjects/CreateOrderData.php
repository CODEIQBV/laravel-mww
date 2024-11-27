<?php

namespace YourNamespace\MyOnlineStore\DataTransferObjects;

class CreateOrderData
{
    public function __construct(
        public readonly int $status,
        public readonly bool $archived,
        public readonly ?array $comments,
        public readonly ?string $reference,
        public readonly ?string $offline_location_id,
        public readonly ?string $discountcode,
        public readonly string $business_model,
        public readonly array $payment,
        public readonly array $debtor,
        public readonly array $orderlines,
        public readonly ?string $shipping_method_id = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            status: $data['status'],
            archived: $data['archived'] ?? false,
            comments: $data['comments'] ?? null,
            reference: $data['reference'] ?? null,
            offline_location_id: $data['offline_location_id'] ?? null,
            discountcode: $data['discountcode'] ?? null,
            business_model: $data['business_model'],
            payment: $data['payment'],
            debtor: $data['debtor'],
            orderlines: $data['orderlines'],
            shipping_method_id: $data['shipping_method_id'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'status' => $this->status,
            'archived' => $this->archived,
            'comments' => $this->comments,
            'reference' => $this->reference,
            'offline_location_id' => $this->offline_location_id,
            'discountcode' => $this->discountcode,
            'business_model' => $this->business_model,
            'payment' => $this->payment,
            'debtor' => $this->debtor,
            'orderlines' => $this->orderlines,
            'shipping_method_id' => $this->shipping_method_id,
        ], fn($value) => !is_null($value));
    }
} 