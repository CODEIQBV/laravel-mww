<?php

namespace YourNamespace\MyOnlineStore\Builders;

class PaymentQueryBuilder
{
    protected $client;
    protected $filters = [];

    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * Get all available payment gateways and methods
     * 
     * @param bool $raw Whether to return raw response or use Resource
     * @return array|\YourNamespace\MyOnlineStore\Resources\PaymentGatewayListResource
     */
    public function gateways(bool $raw = false)
    {
        return $this->client->getPaymentGateways([
            'language' => $this->filters['language'] ?? null,
        ], $raw);
    }

    /**
     * Get payment gateways for a specific store
     * 
     * @param string $storeId Store UUID or 'me'
     * @param bool $raw Whether to return raw response or use Resource
     * @return array|\YourNamespace\MyOnlineStore\Resources\PaymentGatewayListResource
     */
    public function storeGateways(string $storeId = 'me', bool $raw = false)
    {
        return $this->client->getStorePaymentGateways($storeId, [
            'language' => $this->filters['language'] ?? null,
        ], $raw);
    }

    /**
     * Set language for the query
     */
    public function language(string $language)
    {
        $this->filters['language'] = $language;
        return $this;
    }
} 