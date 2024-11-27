<?php

namespace YourNamespace\MyOnlineStore\Builders;

class ShippingQueryBuilder
{
    protected $client;
    protected $filters = [];

    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * Get all available shipping methods
     * 
     * @param bool $raw Whether to return raw response or use Resource
     * @return array|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function methods(bool $raw = false)
    {
        return $this->client->getShippingMethods([], $raw);
    }
} 