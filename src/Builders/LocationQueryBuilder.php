<?php

namespace YourNamespace\MyOnlineStore\Builders;

class LocationQueryBuilder
{
    protected $client;
    protected $filters = [];

    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * Include deleted locations in the response
     */
    public function withDeleted(bool $include = true)
    {
        $this->filters['deleted'] = $include;
        return $this;
    }

    /**
     * Get all offline locations
     * 
     * @param bool $raw Whether to return raw response or use Resource
     * @return array|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function get(bool $raw = false)
    {
        return $this->client->getOfflineLocations($this->filters, $raw);
    }

    /**
     * Get a specific offline location by ID
     * 
     * @param string $locationId
     * @param bool $raw Whether to return raw response or use Resource
     * @return array|\YourNamespace\MyOnlineStore\Resources\OfflineLocationResource
     */
    public function find(string $locationId, bool $raw = false)
    {
        return $this->client->getOfflineLocation($locationId, [], $raw);
    }
} 