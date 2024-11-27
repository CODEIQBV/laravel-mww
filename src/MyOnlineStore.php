<?php

namespace YourNamespace\MyOnlineStore;

use Illuminate\Support\Facades\Http;
use YourNamespace\MyOnlineStore\Exceptions\AuthenticationException;
use YourNamespace\MyOnlineStore\Exceptions\ConfigurationException;
use YourNamespace\MyOnlineStore\Resources\OrderResource;
use YourNamespace\MyOnlineStore\DataTransferObjects\CreateOrderData;
use YourNamespace\MyOnlineStore\DataTransferObjects\UpdateOrderData;
use YourNamespace\MyOnlineStore\DataTransferObjects\CreateCreditOrderData;
use YourNamespace\MyOnlineStore\Resources\PaymentListResource;
use YourNamespace\MyOnlineStore\DataTransferObjects\CreatePaymentData;
use YourNamespace\MyOnlineStore\Resources\CreatePaymentResource;
use YourNamespace\MyOnlineStore\Exceptions\ValidationException;
use YourNamespace\MyOnlineStore\DataTransferObjects\UpdatePaymentData;
use YourNamespace\MyOnlineStore\Resources\OrderStatusResource;
use YourNamespace\MyOnlineStore\Resources\PaymentGatewayListResource;
use YourNamespace\MyOnlineStore\Builders\PaymentQueryBuilder;
use YourNamespace\MyOnlineStore\Resources\ShippingMethodResource;
use YourNamespace\MyOnlineStore\Builders\ShippingQueryBuilder;
use YourNamespace\MyOnlineStore\Resources\OfflineLocationResource;
use YourNamespace\MyOnlineStore\Builders\LocationQueryBuilder;
use YourNamespace\MyOnlineStore\Resources\DiscountCodeResource;
use YourNamespace\MyOnlineStore\Builders\DiscountCodeQueryBuilder;
use YourNamespace\MyOnlineStore\DataTransferObjects\CreateDiscountCodeData;
use YourNamespace\MyOnlineStore\DataTransferObjects\UpdateDiscountCodeData;
use YourNamespace\MyOnlineStore\Resources\ArticleResource;
use YourNamespace\MyOnlineStore\Builders\ArticleQueryBuilder;
use YourNamespace\MyOnlineStore\DataTransferObjects\CreateArticleData;
use YourNamespace\MyOnlineStore\DataTransferObjects\UpdateArticleData;

class MyOnlineStore
{
    protected $config;
    protected $apiKey;
    protected $apiUrl;
    protected $version;
    protected $language;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->apiKey = $config['api_key'];
        $this->apiUrl = $config['api_url'];
        $this->version = $config['api_version'];
        $this->language = $config['language'];
    }

    public function setTenantCredentials(string $apiKey, string $apiUrl = null)
    {
        if (!$this->config['multi_tenant']) {
            throw new ConfigurationException('Multi-tenant mode is not enabled');
        }

        $this->apiKey = $apiKey;
        $this->apiUrl = $apiUrl ?? $this->apiUrl;

        return $this;
    }

    public function articles()
    {
        return new ArticleQueryBuilder($this);
    }

    /**
     * Get orders list with optional filtering
     * 
     * @param array $filters Available filters:
     *   - limit (int) Max 100, default 10
     *   - offset (int)
     *   - start_date (string) Format: Y-m-d
     *   - end_date (string) Format: Y-m-d
     *   - status_id (int)
     *   - archived (bool)
     *   - debtor_email (string)
     *   - debtor_id (string)
     *   - test (bool)
     *   - ordering (string) 'asc' or 'desc'
     * @param bool $raw Whether to return raw response or use Resource
     * @return array|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function orders(array $filters = [], bool $raw = true)
    {
        $queryParams = array_merge([
            'language' => $this->language,
            'format' => 'json',
            'limit' => 10,
        ], $filters);

        $response = $this->makeRequest('GET', "/v{$this->version}/orders", [], $queryParams);

        if ($raw) {
            return $response;
        }

        return OrderResource::collection($response);
    }

    /**
     * Fluent helper for common order queries
     */
    public function ordersList()
    {
        return new OrderQueryBuilder($this);
    }

    /**
     * Get store information
     * 
     * @param string|null $language Shop language to return content for
     * @param string $format Response format (json|xml)
     * @return array
     */
    public function getStoreInformation(?string $language = null, string $format = 'json')
    {
        $queryParams = [
            'language' => $language ?? $this->language,
            'format' => $format,
        ];

        return $this->makeRequest('GET', "/v{$this->version}/store", [], $queryParams);
    }

    /**
     * Create a new order
     * 
     * @param CreateOrderData|array $orderData
     * @param array $options Additional options:
     *   - send_customer_notification (bool) Default: false
     *   - send_merchant_notification (bool) Default: false
     *   - override_stock (bool) Default: false
     *   - disable_shipping (bool) Default: false
     * @param bool $raw Whether to return raw response or use Resource
     * @return array|\YourNamespace\MyOnlineStore\Resources\OrderResource
     */
    public function createOrder(CreateOrderData|array $orderData, array $options = [], bool $raw = false)
    {
        $data = $orderData instanceof CreateOrderData 
            ? $orderData->toArray() 
            : $orderData;

        $queryParams = array_merge([
            'language' => $this->language,
            'format' => 'json',
            'send_customer_notification' => false,
            'send_merchant_notification' => false,
            'override_stock' => false,
            'disable_shipping' => false,
        ], $options);

        $response = $this->makeRequest('POST', "/v{$this->version}/orders", $data, $queryParams);

        if ($raw) {
            return $response;
        }

        return new OrderResource($response);
    }

    /**
     * Get the count of orders with optional filtering
     * 
     * @param array $filters Available filters:
     *   - start_date (string) Format: Y-m-d
     *   - end_date (string) Format: Y-m-d
     *   - status_id (int)
     *   - archived (bool)
     *   - status_changed_start_date (string) Format: Y-m-d H:i:s
     *   - status_changed_end_date (string) Format: Y-m-d H:i:s
     *   - test (bool)
     * @return int
     */
    public function getOrdersCount(array $filters = []): int
    {
        $queryParams = array_merge([
            'language' => $this->language,
            'format' => 'json',
            'test' => false,
        ], $filters);

        $response = $this->makeRequest('GET', "/v{$this->version}/orders/count", [], $queryParams);

        return $response['count'];
    }

    /**
     * Get a specific order by order number
     * 
     * @param int $orderNumber
     * @param array $options Additional options:
     *   - language (string) Default: configured language
     *   - format (string) Default: json
     * @param bool $raw Whether to return raw response or use Resource
     * @return array|\YourNamespace\MyOnlineStore\Resources\OrderResource
     */
    public function getOrder(int $orderNumber, array $options = [], bool $raw = false)
    {
        $queryParams = array_merge([
            'language' => $this->language,
            'format' => 'json',
        ], $options);

        $response = $this->makeRequest('GET', "/v{$this->version}/orders/{$orderNumber}", [], $queryParams);

        if ($raw) {
            return $response;
        }

        return new OrderResource($response);
    }

    /**
     * Update a specific order
     * 
     * @param int $orderNumber
     * @param UpdateOrderData|array $orderData
     * @param array $options Additional options:
     *   - language (string) Default: configured language
     *   - format (string) Default: json
     *   - override_minimum (bool) Default: false
     *   - override_stock (bool) Default: false
     *   - mutate_stock (bool) Default: true
     * @param bool $raw Whether to return raw response or use Resource
     * @return array|\YourNamespace\MyOnlineStore\Resources\OrderResource
     */
    public function updateOrder(
        int $orderNumber, 
        UpdateOrderData|array $orderData, 
        array $options = [], 
        bool $raw = false
    ) {
        $data = $orderData instanceof UpdateOrderData 
            ? $orderData->toArray() 
            : $orderData;

        $queryParams = array_merge([
            'language' => $this->language,
            'format' => 'json',
            'override_minimum' => false,
            'override_stock' => false,
            'mutate_stock' => true,
        ], $options);

        $response = $this->makeRequest('PATCH', "/v{$this->version}/orders/{$orderNumber}", $data, $queryParams);

        if ($raw) {
            return $response;
        }

        return new OrderResource($response);
    }

    /**
     * Create a credit order for an existing order
     * 
     * @param CreateCreditOrderData|array $creditOrderData
     * @param array $options Additional options:
     *   - language (string) Default: configured language
     *   - format (string) Default: json
     *   - mutate_stock (bool) Default: true
     * @param bool $raw Whether to return raw response or use Resource
     * @return array|\YourNamespace\MyOnlineStore\Resources\OrderResource
     */
    public function createCreditOrder(
        CreateCreditOrderData|array $creditOrderData, 
        array $options = [], 
        bool $raw = false
    ) {
        $data = $creditOrderData instanceof CreateCreditOrderData 
            ? $creditOrderData->toArray() 
            : $creditOrderData;

        $queryParams = array_merge([
            'language' => $this->language,
            'format' => 'json',
            'mutate_stock' => true,
        ], $options);

        $response = $this->makeRequest('POST', "/v{$this->version}/orders/credit", $data, $queryParams);

        if ($raw) {
            return $response;
        }

        return new OrderResource($response);
    }

    /**
     * Get payments for a specific order
     * 
     * @param int $orderNumber
     * @param array $options Additional options:
     *   - embed (string|array) Include additional data like 'properties', 'mutations', or 'order'
     * @param bool $raw Whether to return raw response or use Resource
     * @return array|\YourNamespace\MyOnlineStore\Resources\PaymentListResource
     */
    public function getOrderPayments(int $orderNumber, array $options = [], bool $raw = false)
    {
        $queryParams = [];
        
        if (isset($options['embed'])) {
            $queryParams['embed'] = is_array($options['embed']) 
                ? implode(',', $options['embed']) 
                : $options['embed'];
        }

        $response = $this->makeRequest(
            'GET', 
            "/v{$this->version}/orders/{$orderNumber}/payments", 
            [], 
            $queryParams
        );

        if ($raw) {
            return $response;
        }

        return new PaymentListResource($response);
    }

    /**
     * Create a new payment for an order
     * 
     * @param int $orderNumber
     * @param CreatePaymentData|array $paymentData
     * @param bool $raw Whether to return raw response or use Resource
     * @return array|\YourNamespace\MyOnlineStore\Resources\CreatePaymentResource
     * @throws \YourNamespace\MyOnlineStore\Exceptions\ValidationException
     */
    public function createOrderPayment(
        int $orderNumber, 
        CreatePaymentData|array $paymentData, 
        bool $raw = false
    ) {
        $data = $paymentData instanceof CreatePaymentData 
            ? $paymentData->toArray() 
            : $paymentData;

        try {
            $response = $this->makeRequest(
                'POST', 
                "/v{$this->version}/orders/{$orderNumber}/payments", 
                $data
            );
        } catch (\Illuminate\Http\Client\RequestException $e) {
            if ($e->response->status() === 422) {
                throw new ValidationException(
                    $e->response->json()['detail'] ?? 'Invalid payment data',
                    $e->response->json()
                );
            }
            throw $e;
        }

        if ($raw) {
            return $response;
        }

        return new CreatePaymentResource($response);
    }

    /**
     * Update a specific payment
     * 
     * @param int $orderNumber
     * @param string $paymentId
     * @param UpdatePaymentData|array $paymentData
     * @param bool $raw Whether to return raw response or use Resource
     * @return array|\YourNamespace\MyOnlineStore\Resources\CreatePaymentResource
     * @throws \YourNamespace\MyOnlineStore\Exceptions\ValidationException
     */
    public function updateOrderPayment(
        int $orderNumber,
        string $paymentId,
        UpdatePaymentData|array $paymentData,
        bool $raw = false
    ) {
        $data = $paymentData instanceof UpdatePaymentData 
            ? $paymentData->toArray() 
            : $paymentData;

        try {
            $response = $this->makeRequest(
                'PATCH',
                "/v{$this->version}/orders/{$orderNumber}/payments/{$paymentId}",
                $data
            );
        } catch (\Illuminate\Http\Client\RequestException $e) {
            if ($e->response->status() === 422) {
                throw new ValidationException(
                    $e->response->json()['detail'] ?? 'Invalid payment data',
                    $e->response->json()
                );
            }
            throw $e;
        }

        if ($raw) {
            return $response;
        }

        return new CreatePaymentResource($response);
    }

    /**
     * Delete a specific payment
     * 
     * @param int $orderNumber
     * @param string $paymentId
     * @return bool Success status
     */
    public function deleteOrderPayment(int $orderNumber, string $paymentId): bool
    {
        $response = $this->makeRequest(
            'DELETE',
            "/v{$this->version}/orders/{$orderNumber}/payments/{$paymentId}"
        );

        // DELETE requests with 204 response don't return any content
        return true;
    }

    /**
     * Get all available order statuses
     * 
     * @param array $options Additional options:
     *   - language (string) Default: configured language
     *   - format (string) Default: json
     * @param bool $raw Whether to return raw response or use Resource
     * @return array|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getOrderStatuses(array $options = [], bool $raw = false)
    {
        $queryParams = array_merge([
            'language' => $this->language,
            'format' => 'json',
        ], $options);

        $response = $this->makeRequest('GET', "/v{$this->version}/orderstatuses", [], $queryParams);

        if ($raw) {
            return $response;
        }

        return OrderStatusResource::collection($response);
    }

    /**
     * Get all available payment gateways and methods
     * 
     * @param array $options Additional options:
     *   - language (string) Default: configured language
     *   - format (string) Default: json
     * @param bool $raw Whether to return raw response or use Resource
     * @return array|\YourNamespace\MyOnlineStore\Resources\PaymentGatewayListResource
     */
    public function getPaymentGateways(array $options = [], bool $raw = false)
    {
        $queryParams = array_merge([
            'language' => $this->language,
            'format' => 'json',
        ], $options);

        $response = $this->makeRequest('GET', "/v{$this->version}/payment/gateways", [], $queryParams);

        if ($raw) {
            return $response;
        }

        return new PaymentGatewayListResource($response);
    }

    /**
     * Fluent helper for payment related queries
     */
    public function payments()
    {
        return new PaymentQueryBuilder($this);
    }

    /**
     * Get payment gateways for a specific store
     * 
     * @param string $storeId Store UUID or 'me'
     * @param array $options Additional options:
     *   - language (string) Default: configured language
     *   - format (string) Default: json
     * @param bool $raw Whether to return raw response or use Resource
     * @return array|\YourNamespace\MyOnlineStore\Resources\PaymentGatewayListResource
     */
    public function getStorePaymentGateways(string $storeId, array $options = [], bool $raw = false)
    {
        $queryParams = array_merge([
            'language' => $this->language,
            'format' => 'json',
        ], $options);

        $response = $this->makeRequest(
            'GET', 
            "/v{$this->version}/payment/stores/{$storeId}/gateways", 
            [], 
            $queryParams
        );

        if ($raw) {
            return $response;
        }

        return new PaymentGatewayListResource($response);
    }

    /**
     * Get all available shipping methods
     * 
     * @param array $options Additional options:
     *   - format (string) Default: json
     * @param bool $raw Whether to return raw response or use Resource
     * @return array|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getShippingMethods(array $options = [], bool $raw = false)
    {
        $queryParams = array_merge([
            'format' => 'json',
        ], $options);

        $response = $this->makeRequest('GET', "/v{$this->version}/shipping/methods", [], $queryParams);

        if ($raw) {
            return $response;
        }

        return ShippingMethodResource::collection($response);
    }

    /**
     * Fluent helper for shipping related queries
     */
    public function shipping()
    {
        return new ShippingQueryBuilder($this);
    }

    /**
     * Get all offline (POS) locations
     * 
     * @param array $options Additional options:
     *   - format (string) Default: json
     *   - deleted (bool|null) Filter deleted locations
     * @param bool $raw Whether to return raw response or use Resource
     * @return array|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getOfflineLocations(array $options = [], bool $raw = false)
    {
        $queryParams = array_merge([
            'format' => 'json',
        ], $options);

        $response = $this->makeRequest('GET', "/v{$this->version}/offlinelocations", [], $queryParams);

        if ($raw) {
            return $response;
        }

        return OfflineLocationResource::collection($response);
    }

    /**
     * Fluent helper for offline location related queries
     */
    public function locations()
    {
        return new LocationQueryBuilder($this);
    }

    /**
     * Get a specific offline location by ID
     * 
     * @param string $locationId
     * @param array $options Additional options:
     *   - format (string) Default: json
     * @param bool $raw Whether to return raw response or use Resource
     * @return array|\YourNamespace\MyOnlineStore\Resources\OfflineLocationResource
     */
    public function getOfflineLocation(string $locationId, array $options = [], bool $raw = false)
    {
        $queryParams = array_merge([
            'format' => 'json',
        ], $options);

        $response = $this->makeRequest(
            'GET', 
            "/v{$this->version}/offlinelocations/{$locationId}", 
            [], 
            $queryParams
        );

        if ($raw) {
            return $response;
        }

        return new OfflineLocationResource($response);
    }

    /**
     * Get all discount codes
     * 
     * @param array $options Additional options:
     *   - format (string) Default: json
     *   - active (bool|null) Only (in)active discount codes
     *   - valid_start_date (string) Format: Y-m-d
     *   - valid_end_date (string) Format: Y-m-d
     * @param bool $raw Whether to return raw response or use Resource
     * @return array|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getDiscountCodes(array $options = [], bool $raw = false)
    {
        $queryParams = array_merge([
            'format' => 'json',
        ], $options);

        $response = $this->makeRequest('GET', "/v{$this->version}/discountcodes", [], $queryParams);

        if ($raw) {
            return $response;
        }

        return DiscountCodeResource::collection($response);
    }

    /**
     * Fluent helper for discount code related queries
     */
    public function discountCodes()
    {
        return new DiscountCodeQueryBuilder($this);
    }

    /**
     * Create a new discount code
     * 
     * @param CreateDiscountCodeData|array $discountCodeData
     * @param array $options Additional options:
     *   - format (string) Default: json
     * @param bool $raw Whether to return raw response or use Resource
     * @return array|\YourNamespace\MyOnlineStore\Resources\DiscountCodeResource
     */
    public function createDiscountCode(
        CreateDiscountCodeData|array $discountCodeData,
        array $options = [],
        bool $raw = false
    ) {
        $data = $discountCodeData instanceof CreateDiscountCodeData 
            ? $discountCodeData->toArray() 
            : $discountCodeData;

        $queryParams = array_merge([
            'format' => 'json',
        ], $options);

        $response = $this->makeRequest('POST', "/v{$this->version}/discountcodes", $data, $queryParams);

        if ($raw) {
            return $response;
        }

        return new DiscountCodeResource($response);
    }

    /**
     * Update a discount code
     * 
     * @param string $discountCodeId
     * @param UpdateDiscountCodeData|array $discountCodeData
     * @param array $options Additional options:
     *   - format (string) Default: json
     * @param bool $raw Whether to return raw response or use Resource
     * @return array|\YourNamespace\MyOnlineStore\Resources\DiscountCodeResource
     */
    public function updateDiscountCode(
        string $discountCodeId,
        UpdateDiscountCodeData|array $discountCodeData,
        array $options = [],
        bool $raw = false
    ) {
        $data = $discountCodeData instanceof UpdateDiscountCodeData 
            ? $discountCodeData->toArray() 
            : $discountCodeData;

        $queryParams = array_merge([
            'format' => 'json',
        ], $options);

        $response = $this->makeRequest(
            'PATCH', 
            "/v{$this->version}/discountcodes/{$discountCodeId}", 
            $data, 
            $queryParams
        );

        if ($raw) {
            return $response;
        }

        return new DiscountCodeResource($response);
    }

    /**
     * Get a specific discount code by code
     * 
     * @param string $code
     * @param array $options Additional options:
     *   - format (string) Default: json
     * @param bool $raw Whether to return raw response or use Resource
     * @return array|\YourNamespace\MyOnlineStore\Resources\DiscountCodeResource
     */
    public function getDiscountCode(string $code, array $options = [], bool $raw = false)
    {
        $queryParams = array_merge([
            'format' => 'json',
        ], $options);

        $response = $this->makeRequest(
            'GET', 
            "/v{$this->version}/discountcodes/{$code}", 
            [], 
            $queryParams
        );

        if ($raw) {
            return $response;
        }

        return new DiscountCodeResource($response);
    }

    /**
     * Delete a specific discount code
     * 
     * @param string $code
     * @param array $options Additional options:
     *   - format (string) Default: json
     * @return bool Success status
     */
    public function deleteDiscountCode(string $code, array $options = []): bool
    {
        $queryParams = array_merge([
            'format' => 'json',
        ], $options);

        $response = $this->makeRequest(
            'DELETE',
            "/v{$this->version}/discountcodes/{$code}",
            [],
            $queryParams
        );

        return true;
    }

    /**
     * Get articles list with optional filtering
     * 
     * @param array $options Additional options:
     *   - language (string) Default: configured language
     *   - format (string) Default: json
     *   - limit (int) Max: 100, Default: 10
     *   - offset (int)
     *   - created_start_date (string) Format: Y-m-d H:i:s
     *   - created_end_date (string) Format: Y-m-d H:i:s
     *   - changed_start_date (string) Format: Y-m-d H:i:s
     *   - changed_end_date (string) Format: Y-m-d H:i:s
     *   - ids (array) Specific article IDs
     *   - uuids (array) Specific article UUIDs
     * @param bool $raw Whether to return raw response or use Resource
     * @return array|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getArticles(array $options = [], bool $raw = false)
    {
        $queryParams = array_merge([
            'language' => $this->language,
            'format' => 'json',
            'limit' => 10,
        ], $options);

        // Convert arrays to comma-separated strings
        if (isset($queryParams['ids']) && is_array($queryParams['ids'])) {
            $queryParams['ids'] = implode(',', $queryParams['ids']);
        }
        if (isset($queryParams['uuids']) && is_array($queryParams['uuids'])) {
            $queryParams['uuids'] = implode(',', $queryParams['uuids']);
        }

        $response = $this->makeRequest('GET', "/v{$this->version}/articles", [], $queryParams);

        if ($raw) {
            return $response;
        }

        return ArticleResource::collection($response);
    }

    /**
     * Create a new article
     * 
     * @param CreateArticleData|array $articleData
     * @param array $options Additional options:
     *   - language (string) Default: configured language
     *   - format (string) Default: json
     * @param bool $raw Whether to return raw response or use Resource
     * @return array|\YourNamespace\MyOnlineStore\Resources\ArticleResource
     */
    public function createArticle(
        CreateArticleData|array $articleData,
        array $options = [],
        bool $raw = false
    ) {
        $data = $articleData instanceof CreateArticleData 
            ? $articleData->toArray() 
            : $articleData;

        $queryParams = array_merge([
            'language' => $this->language,
            'format' => 'json',
        ], $options);

        $response = $this->makeRequest('POST', "/v{$this->version}/articles", $data, $queryParams);

        if ($raw) {
            return $response;
        }

        return new ArticleResource($response);
    }

    /**
     * Get a specific article by ID
     * 
     * @param int $articleId
     * @param array $options Additional options:
     *   - language (string) Default: configured language
     *   - format (string) Default: json
     *   - use_url_id (bool) Default: false
     * @param bool $raw Whether to return raw response or use Resource
     * @return array|\YourNamespace\MyOnlineStore\Resources\ArticleResource
     */
    public function getArticle(int $articleId, array $options = [], bool $raw = false)
    {
        $queryParams = array_merge([
            'language' => $this->language,
            'format' => 'json',
            'use_url_id' => false,
        ], $options);

        $response = $this->makeRequest(
            'GET', 
            "/v{$this->version}/articles/{$articleId}", 
            [], 
            $queryParams
        );

        if ($raw) {
            return $response;
        }

        return new ArticleResource($response);
    }

    /**
     * Delete a specific article
     * 
     * @param int $articleId
     * @param array $options Additional options:
     *   - language (string) Default: configured language
     *   - use_url_id (bool) Default: false
     * @return bool Success status
     */
    public function deleteArticle(int $articleId, array $options = []): bool
    {
        $queryParams = array_merge([
            'language' => $this->language,
            'use_url_id' => false,
        ], $options);

        $response = $this->makeRequest(
            'DELETE',
            "/v{$this->version}/articles/{$articleId}",
            [],
            $queryParams
        );

        return true;
    }

    /**
     * Update a specific article
     * 
     * @param int $articleId
     * @param UpdateArticleData|array $articleData
     * @param array $options Additional options:
     *   - language (string) Default: configured language
     *   - format (string) Default: json
     *   - use_url_id (bool) Default: false
     * @param bool $raw Whether to return raw response or use Resource
     * @return array|\YourNamespace\MyOnlineStore\Resources\ArticleResource
     */
    public function updateArticle(
        int $articleId,
        UpdateArticleData|array $articleData,
        array $options = [],
        bool $raw = false
    ) {
        $data = $articleData instanceof UpdateArticleData 
            ? $articleData->toArray() 
            : $articleData;

        $queryParams = array_merge([
            'language' => $this->language,
            'format' => 'json',
            'use_url_id' => false,
        ], $options);

        $response = $this->makeRequest(
            'PATCH',
            "/v{$this->version}/articles/{$articleId}",
            $data,
            $queryParams
        );

        if ($raw) {
            return $response;
        }

        return new ArticleResource($response);
    }

    /**
     * Delete an image from an article
     * 
     * @param int $imageId
     * @param array $options Additional options:
     *   - language (string) Default: configured language
     *   - format (string) Default: json
     * @return bool Success status
     */
    public function deleteArticleImage(int $imageId, array $options = []): bool
    {
        $queryParams = array_merge([
            'language' => $this->language,
            'format' => 'json',
        ], $options);

        $response = $this->makeRequest(
            'DELETE',
            "/v{$this->version}/deleteImage/{$imageId}",
            [],
            $queryParams
        );

        // DELETE requests with 204 response don't return any content
        return true;
    }

    /**
     * Get the count of articles with optional filtering
     * 
     * @param array $options Additional options:
     *   - language (string) Default: configured language
     *   - format (string) Default: json
     *   - created_start_date (string) Format: Y-m-d H:i:s
     *   - created_end_date (string) Format: Y-m-d H:i:s
     *   - changed_start_date (string) Format: Y-m-d H:i:s
     *   - changed_end_date (string) Format: Y-m-d H:i:s
     * @return int
     */
    public function getArticlesCount(array $options = []): int
    {
        $queryParams = array_merge([
            'language' => $this->language,
            'format' => 'json',
        ], $options);

        $response = $this->makeRequest('GET', "/v{$this->version}/articles/count", [], $queryParams);

        return $response['count'];
    }

    protected function makeRequest(string $method, string $endpoint, array $data = [], array $query = [])
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept' => 'application/json',
        ])
        ->timeout($this->config['timeout'])
        ->withQueryParameters($query)
        ->{strtolower($method)}($this->apiUrl . $endpoint, $data);

        if ($response->status() === 401) {
            throw new AuthenticationException('Invalid API credentials');
        }

        // For DELETE requests that return 204 No Content
        if ($method === 'DELETE' && $response->status() === 204) {
            return true;
        }

        return $response->json();
    }
} 