<?php

namespace YourNamespace\MyOnlineStore\DataTransferObjects;

class UpdateOrderData
{
    public function __construct(
        public readonly ?int $status = null,
        public readonly ?bool $archived = null,
        public readonly ?array $comments = null,
        public readonly ?string $reference = null,
        public readonly ?string $offline_location_id = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            status: $data['status'] ?? null,
            archived: $data['archived'] ?? null,
            comments: $data['comments'] ?? null,
            reference: $data['reference'] ?? null,
            offline_location_id: $data['offline_location_id'] ?? null,
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
        ], fn($value) => !is_null($value));
    }
} 