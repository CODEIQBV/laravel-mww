<?php

use YourNamespace\MyOnlineStore\MyOnlineStore;
use YourNamespace\MyOnlineStore\DataTransferObjects\CreateOrderData;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->client = new MyOnlineStore([
        'api_key' => 'test-api-key',
        'api_url' => 'https://api.myonlinestore.test',
        'api_version' => '1',
        'language' => 'nl_NL',
        'timeout' => 30,
    ]);
});

it('can get orders list', function () {
    Http::fake([
        'api.myonlinestore.test/v1/orders*' => Http::response([
            ['number' => 1, 'status' => 'pending'],
            ['number' => 2, 'status' => 'completed'],
        ]),
    ]);

    $orders = $this->client->orders();

    expect($orders)->toBeArray()
        ->toHaveCount(2)
        ->and($orders[0])->toHaveKey('status', 'pending');
});

it('can get orders count', function () {
    Http::fake([
        'api.myonlinestore.test/v1/orders/count' => Http::response([
            'count' => 15
        ]),
    ]);

    $count = $this->client->getOrdersCount();

    expect($count)->toBe(15);
});

it('supports fluent query builder', function () {
    Http::fake([
        'api.myonlinestore.test/v1/orders*' => Http::response([
            ['number' => 1, 'status' => 'pending'],
        ]),
    ]);

    $orders = $this->client->ordersList()
        ->limit(10)
        ->offset(20)
        ->between('2024-01-01', '2024-03-01')
        ->status(1)
        ->archived(true)
        ->get();

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://api.myonlinestore.test/v1/orders' &&
            $request['limit'] === 10 &&
            $request['offset'] === 20 &&
            $request['start_date'] === '2024-01-01' &&
            $request['end_date'] === '2024-03-01' &&
            $request['status_id'] === 1 &&
            $request['archived'] === true;
    });
}); 