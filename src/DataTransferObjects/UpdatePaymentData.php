<?php

namespace YourNamespace\MyOnlineStore\DataTransferObjects;

class UpdatePaymentData
{
    public function __construct(
        public readonly ?string $method = null,
        public readonly ?string $referrerUrl = null,
        public readonly ?array $orderLines = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            method: $data['method'] ?? null,
            referrerUrl: $data['referrerUrl'] ?? null,
            orderLines: $data['orderLines'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'method' => $this->method,
            'referrerUrl' => $this->referrerUrl,
            'orderLines' => $this->orderLines,
        ], fn($value) => !is_null($value));
    }
} 