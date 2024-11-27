<?php

namespace YourNamespace\MyOnlineStore\DataTransferObjects;

class CreatePaymentData
{
    public function __construct(
        public readonly string $gateway,
        public readonly string $method,
        public readonly ?string $referrerUrl = null,
        public readonly ?array $orderLines = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            gateway: $data['gateway'],
            method: $data['method'],
            referrerUrl: $data['referrerUrl'] ?? null,
            orderLines: $data['orderLines'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'gateway' => $this->gateway,
            'method' => $this->method,
            'referrerUrl' => $this->referrerUrl,
            'orderLines' => $this->orderLines,
        ], fn($value) => !is_null($value));
    }
} 