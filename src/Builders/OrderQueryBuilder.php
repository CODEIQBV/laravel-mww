<?php

namespace YourNamespace\MyOnlineStore\Builders;

class OrderQueryBuilder
{
    protected $client;
    protected $filters = [];

    public function __construct($client)
    {
        $this->client = $client;
    }

    public function limit(int $limit)
    {
        $this->filters['limit'] = min($limit, 100);
        return $this;
    }

    public function offset(int $offset)
    {
        $this->filters['offset'] = $offset;
        return $this;
    }

    public function between(string $startDate, string $endDate)
    {
        $this->filters['start_date'] = $startDate;
        $this->filters['end_date'] = $endDate;
        return $this;
    }

    public function status(int $statusId)
    {
        $this->filters['status_id'] = $statusId;
        return $this;
    }

    public function archived(bool $archived = true)
    {
        $this->filters['archived'] = $archived;
        return $this;
    }

    public function forDebtor(string $emailOrId)
    {
        if (str_contains($emailOrId, '@')) {
            $this->filters['debtor_email'] = $emailOrId;
        } else {
            $this->filters['debtor_id'] = $emailOrId;
        }
        return $this;
    }

    public function orderBy(string $direction = 'asc')
    {
        $this->filters['ordering'] = $direction;
        return $this;
    }

    public function get(bool $raw = true)
    {
        return $this->client->orders($this->filters, $raw);
    }

    /**
     * Add status changed date range filter
     */
    public function statusChangedBetween(string $startDateTime, string $endDateTime)
    {
        $this->filters['status_changed_start_date'] = $startDateTime;
        $this->filters['status_changed_end_date'] = $endDateTime;
        return $this;
    }

    /**
     * Include test orders
     */
    public function includeTest(bool $include = true)
    {
        $this->filters['test'] = $include;
        return $this;
    }

    /**
     * Get the count of orders matching the current filters
     */
    public function count(): int
    {
        return $this->client->getOrdersCount($this->filters);
    }

    /**
     * Find a specific order by order number
     * 
     * @param int $orderNumber
     * @param bool $raw Whether to return raw response or use Resource
     * @return array|\YourNamespace\MyOnlineStore\Resources\OrderResource
     */
    public function find(int $orderNumber, bool $raw = false)
    {
        return $this->client->getOrder($orderNumber, [
            'language' => $this->filters['language'] ?? null,
        ], $raw);
    }

    /**
     * Update a specific order
     * 
     * @param int $orderNumber
     * @param UpdateOrderData|array $orderData
     * @param array $options Additional options for the update
     * @param bool $raw Whether to return raw response or use Resource
     * @return array|\YourNamespace\MyOnlineStore\Resources\OrderResource
     */
    public function update(
        int $orderNumber, 
        UpdateOrderData|array $orderData, 
        array $options = [], 
        bool $raw = false
    ) {
        return $this->client->updateOrder($orderNumber, $orderData, array_merge([
            'language' => $this->filters['language'] ?? null,
        ], $options), $raw);
    }

    /**
     * Create a credit order for an existing order
     * 
     * @param CreateCreditOrderData|array $creditOrderData
     * @param array $options Additional options for the credit order
     * @param bool $raw Whether to return raw response or use Resource
     * @return array|\YourNamespace\MyOnlineStore\Resources\OrderResource
     */
    public function credit(
        CreateCreditOrderData|array $creditOrderData, 
        array $options = [], 
        bool $raw = false
    ) {
        return $this->client->createCreditOrder($creditOrderData, array_merge([
            'language' => $this->filters['language'] ?? null,
        ], $options), $raw);
    }

    /**
     * Get payments for a specific order
     * 
     * @param int $orderNumber
     * @param array|string|null $embed Include additional data ('properties', 'mutations', 'order')
     * @param bool $raw Whether to return raw response or use Resource
     * @return array|\YourNamespace\MyOnlineStore\Resources\PaymentListResource
     */
    public function payments(int $orderNumber, array|string|null $embed = null, bool $raw = false)
    {
        $options = [];
        if ($embed) {
            $options['embed'] = $embed;
        }

        return $this->client->getOrderPayments($orderNumber, $options, $raw);
    }

    /**
     * Create a new payment for an order
     * 
     * @param int $orderNumber
     * @param CreatePaymentData|array $paymentData
     * @param bool $raw Whether to return raw response or use Resource
     * @return array|\YourNamespace\MyOnlineStore\Resources\CreatePaymentResource
     */
    public function createPayment(
        int $orderNumber, 
        CreatePaymentData|array $paymentData, 
        bool $raw = false
    ) {
        return $this->client->createOrderPayment($orderNumber, $paymentData, $raw);
    }

    /**
     * Update a specific payment
     * 
     * @param int $orderNumber
     * @param string $paymentId
     * @param UpdatePaymentData|array $paymentData
     * @param bool $raw Whether to return raw response or use Resource
     * @return array|\YourNamespace\MyOnlineStore\Resources\CreatePaymentResource
     */
    public function updatePayment(
        int $orderNumber,
        string $paymentId,
        UpdatePaymentData|array $paymentData,
        bool $raw = false
    ) {
        return $this->client->updateOrderPayment($orderNumber, $paymentId, $paymentData, $raw);
    }

    /**
     * Delete a specific payment
     * 
     * @param int $orderNumber
     * @param string $paymentId
     * @return bool Success status
     */
    public function deletePayment(int $orderNumber, string $paymentId): bool
    {
        return $this->client->deleteOrderPayment($orderNumber, $paymentId);
    }

    /**
     * Get all available order statuses
     * 
     * @param bool $raw Whether to return raw response or use Resource
     * @return array|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function statuses(bool $raw = false)
    {
        return $this->client->getOrderStatuses([
            'language' => $this->filters['language'] ?? null,
        ], $raw);
    }
} 