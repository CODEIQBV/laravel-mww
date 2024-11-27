# Laravel MyOnlineStore Integration

[![Latest Version on Packagist](https://img.shields.io/packagist/v/codeiqbv/laravel-mww.svg?style=flat-square)](https://packagist.org/packages/codeiqbv/laravel-mww)
[![Total Downloads](https://img.shields.io/packagist/dt/codeiqbv/laravel-mww.svg?style=flat-square)](https://packagist.org/packages/codeiqbv/laravel-mww)

A Laravel package for seamless integration with the MyOnlineStore API.

## Table of Contents

- [Installation](#installation)
- [Configuration](#configuration)
- [Basic Usage](#basic-usage)
- [Articles](#articles)
- [Orders](#orders)
- [Payments](#payments)
- [Discount Codes](#discount-codes)
- [Shipping Methods](#shipping-methods)
- [Offline Locations](#offline-locations)
- [Query Builders](#query-builders)
- [Resources](#resources)
- [Data Transfer Objects](#data-transfer-objects)
- [Error Handling](#error-handling)
- [Testing](#testing)
- [Multi-tenant Usage](#multi-tenant-usage)

## Installation

You can install the package via composer:

```bash
composer require codeiqbv/laravel-mww
```

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag="myonlinestore-config"
```

Add these environment variables to your `.env` file:

```env
MYONLINESTORE_API_KEY=your_api_key
MYONLINESTORE_API_URL=https://api.myonlinestore.com
MYONLINESTORE_API_VERSION=1
MYONLINESTORE_MULTI_TENANT=false
MYONLINESTORE_LANGUAGE=nl_NL
MYONLINESTORE_TIMEOUT=30
```

## Basic Usage

The package provides a fluent interface for all API operations:

```php
use CodeIQ\LaravelMww\Facades\MyOnlineStore;

// Get all articles
$articles = MyOnlineStore::articles()->get();

// Get all orders
$orders = MyOnlineStore::ordersList()->get();
```

## Articles

### Listing Articles

```php
// Basic listing
$articles = MyOnlineStore::articles()->get();

// With filters
$articles = MyOnlineStore::articles()
    ->limit(50)
    ->offset(0)
    ->createdBetween('2024-01-01 00:00:00', '2024-03-01 23:59:59')
    ->language('en_GB')
    ->get();

// Get count
$count = MyOnlineStore::articles()->count();
```

### Single Article Operations

```php
// Get single article
$article = MyOnlineStore::articles()->find(123);

// Create article
$articleData = new CreateArticleData(
    name: 'New Product',
    description: 'Product description',
    sku: 'PROD-123',
    price: [
        'default' => 12.50,
        'action' => 9.95
    ]
);
$article = MyOnlineStore::articles()->create($articleData);

// Update article
$updateData = new UpdateArticleData(
    name: 'Updated Name',
    stock: 20
);
$article = MyOnlineStore::articles()->update(123, $updateData);

// Delete article
$success = MyOnlineStore::articles()->delete(123);

// Delete article image
$success = MyOnlineStore::articles()->deleteImage(456);
```

## Orders

### Listing Orders

```php
// Basic listing
$orders = MyOnlineStore::ordersList()->get();

// With filters
$orders = MyOnlineStore::ordersList()
    ->limit(25)
    ->between('2024-01-01', '2024-03-01')
    ->status(1)
    ->archived(true)
    ->forDebtor('customer@example.com')
    ->orderBy('desc')
    ->get();

// Get count
$count = MyOnlineStore::ordersList()->count();
```

### Single Order Operations

```php
// Get single order
$order = MyOnlineStore::ordersList()->find(27);

// Create order
$orderData = new CreateOrderData(
    // ... order details
);
$order = MyOnlineStore::ordersList()->create($orderData);

// Update order
$updateData = new UpdateOrderData(
    status: 2,
    archived: true
);
$order = MyOnlineStore::ordersList()->update(27, $updateData);

// Create credit order
$creditData = new CreateCreditOrderData(
    credited_order_number: '272',
    status: 10
);
$creditOrder = MyOnlineStore::ordersList()->credit($creditData);
```

## Payments

### Order Payments

```php
// Get order payments
$payments = MyOnlineStore::ordersList()
    ->payments(27, ['properties', 'mutations']);

// Create payment
$paymentData = new CreatePaymentData(
    gateway: 'mollie',
    method: 'ideal'
);
$payment = MyOnlineStore::ordersList()
    ->createPayment(27, $paymentData);

// Update payment
$updateData = new UpdatePaymentData(
    method: 'creditcard'
);
$payment = MyOnlineStore::ordersList()
    ->updatePayment(27, 'payment-id', $updateData);

// Delete payment
$success = MyOnlineStore::ordersList()
    ->deletePayment(27, 'payment-id');
```

### Payment Gateways

```php
// Get all gateways
$gateways = MyOnlineStore::payments()->gateways();

// Get store-specific gateways
$gateways = MyOnlineStore::payments()
    ->storeGateways('store-id');
```

## Discount Codes

```php
// List discount codes
$codes = MyOnlineStore::discountCodes()
    ->active()
    ->validBetween('2024-01-01', '2024-12-31')
    ->get();

// Create discount code
$codeData = new CreateDiscountCodeData(
    description: 'Summer Sale',
    percentage_discount: '10.00'
);
$code = MyOnlineStore::discountCodes()->create($codeData);

// Get single code
$code = MyOnlineStore::discountCodes()->find('SUMMER10');

// Update code
$updateData = new UpdateDiscountCodeData(
    active: false
);
$code = MyOnlineStore::discountCodes()->update('SUMMER10', $updateData);

// Delete code
$success = MyOnlineStore::discountCodes()->delete('SUMMER10');
```

## Query Builders

The package provides fluent query builders for:

- `ArticleQueryBuilder`
- `OrderQueryBuilder`
- `PaymentQueryBuilder`
- `DiscountCodeQueryBuilder`
- `LocationQueryBuilder`
- `ShippingQueryBuilder`

Each builder provides methods for filtering and manipulating the respective resources.

## Resources

API responses are transformed through Laravel Resources:

- `ArticleResource`
- `OrderResource`
- `PaymentResource`
- `DiscountCodeResource`
- `OrderLineResource`
- `PriceResource`
- `TaxResource`
- `AddressResource`
- `DebtorResource`
- And more...

## Data Transfer Objects

Type-safe DTOs for creating and updating resources:

- `CreateArticleData`
- `UpdateArticleData`
- `CreateOrderData`
- `UpdateOrderData`
- `CreatePaymentData`
- `UpdatePaymentData`
- `CreateDiscountCodeData`
- `UpdateDiscountCodeData`
- And more...

## Error Handling

The package throws these exceptions:

```php
use YourNamespace\MyOnlineStore\Exceptions\AuthenticationException;
use YourNamespace\MyOnlineStore\Exceptions\ConfigurationException;
use YourNamespace\MyOnlineStore\Exceptions\ValidationException;

try {
    $result = MyOnlineStore::articles()->create($data);
} catch (AuthenticationException $e) {
    // Handle invalid API credentials
} catch (ValidationException $e) {
    $errors = $e->getErrors();
    // Handle validation errors
} catch (ConfigurationException $e) {
    // Handle configuration issues
}
```

## Testing

```bash
composer test
```

## Multi-tenant Usage

For multi-tenant applications:

```php
// Enable multi-tenant mode in config
'multi_tenant' => true,

// Set tenant-specific credentials
MyOnlineStore::setTenantCredentials(
    'tenant-api-key',
    'https://tenant-specific-url.com'
);

// Continue using the API as normal
$articles = MyOnlineStore::articles()->get();
```

## Security

If you discover any security related issues, please email security@codeiq.nl instead of using the issue tracker.

## Credits

- [CodeIQ](https://github.com/CODEIQBV)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
