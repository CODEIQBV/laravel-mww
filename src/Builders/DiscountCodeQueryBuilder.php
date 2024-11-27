<?php

namespace YourNamespace\MyOnlineStore\Builders;

class DiscountCodeQueryBuilder
{
    protected $client;
    protected $filters = [];

    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * Filter by active status
     */
    public function active(?bool $active = true)
    {
        $this->filters['active'] = $active;
        return $this;
    }

    /**
     * Filter by validity period
     */
    public function validBetween(?string $startDate, ?string $endDate)
    {
        if ($startDate) {
            $this->filters['valid_start_date'] = $startDate;
        }
        if ($endDate) {
            $this->filters['valid_end_date'] = $endDate;
        }
        return $this;
    }

    /**
     * Get all discount codes
     * 
     * @param bool $raw Whether to return raw response or use Resource
     * @return array|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function get(bool $raw = false)
    {
        return $this->client->getDiscountCodes($this->filters, $raw);
    }

    /**
     * Create a new discount code
     * 
     * @param CreateDiscountCodeData|array $discountCodeData
     * @param bool $raw Whether to return raw response or use Resource
     * @return array|\YourNamespace\MyOnlineStore\Resources\DiscountCodeResource
     */
    public function create(CreateDiscountCodeData|array $discountCodeData, bool $raw = false)
    {
        return $this->client->createDiscountCode($discountCodeData, [], $raw);
    }

    /**
     * Update a discount code
     * 
     * @param string $discountCodeId
     * @param UpdateDiscountCodeData|array $discountCodeData
     * @param bool $raw Whether to return raw response or use Resource
     * @return array|\YourNamespace\MyOnlineStore\Resources\DiscountCodeResource
     */
    public function update(
        string $discountCodeId,
        UpdateDiscountCodeData|array $discountCodeData,
        bool $raw = false
    ) {
        return $this->client->updateDiscountCode($discountCodeId, $discountCodeData, [], $raw);
    }

    /**
     * Get a specific discount code by code
     * 
     * @param string $code
     * @param bool $raw Whether to return raw response or use Resource
     * @return array|\YourNamespace\MyOnlineStore\Resources\DiscountCodeResource
     */
    public function find(string $code, bool $raw = false)
    {
        return $this->client->getDiscountCode($code, [], $raw);
    }

    /**
     * Delete a specific discount code
     * 
     * @param string $code
     * @return bool Success status
     */
    public function delete(string $code): bool
    {
        return $this->client->deleteDiscountCode($code);
    }
} 