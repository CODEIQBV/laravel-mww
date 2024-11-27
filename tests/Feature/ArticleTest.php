<?php

use YourNamespace\MyOnlineStore\MyOnlineStore;
use YourNamespace\MyOnlineStore\DataTransferObjects\CreateArticleData;
use YourNamespace\MyOnlineStore\DataTransferObjects\UpdateArticleData;
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

it('can get articles list', function () {
    Http::fake([
        'api.myonlinestore.test/v1/articles*' => Http::response([
            ['id' => 1, 'name' => 'Test Article'],
            ['id' => 2, 'name' => 'Another Article'],
        ]),
    ]);

    $articles = $this->client->getArticles();

    expect($articles)->toBeArray()
        ->toHaveCount(2)
        ->and($articles[0])->toHaveKey('name', 'Test Article');

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://api.myonlinestore.test/v1/articles' &&
            $request->hasHeader('Authorization', 'Bearer test-api-key');
    });
});

it('can create an article', function () {
    Http::fake([
        'api.myonlinestore.test/v1/articles' => Http::response([
            'id' => 1,
            'name' => 'New Article',
            'description' => 'Test Description',
        ]),
    ]);

    $articleData = new CreateArticleData(
        name: 'New Article',
        description: 'Test Description',
        sku: 'TEST-123'
    );

    $article = $this->client->createArticle($articleData);

    expect($article->name)->toBe('New Article')
        ->and($article->description)->toBe('Test Description');

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://api.myonlinestore.test/v1/articles' &&
            $request->method() === 'POST' &&
            $request['name'] === 'New Article';
    });
});

it('can get a single article', function () {
    Http::fake([
        'api.myonlinestore.test/v1/articles/1' => Http::response([
            'id' => 1,
            'name' => 'Test Article',
            'description' => 'Test Description',
        ]),
    ]);

    $article = $this->client->getArticle(1);

    expect($article->name)->toBe('Test Article')
        ->and($article->description)->toBe('Test Description');

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://api.myonlinestore.test/v1/articles/1' &&
            $request->method() === 'GET';
    });
});

it('can update an article', function () {
    Http::fake([
        'api.myonlinestore.test/v1/articles/1' => Http::response([
            'id' => 1,
            'name' => 'Updated Article',
            'description' => 'Updated Description',
        ]),
    ]);

    $updateData = new UpdateArticleData(
        name: 'Updated Article',
        description: 'Updated Description'
    );

    $article = $this->client->updateArticle(1, $updateData);

    expect($article->name)->toBe('Updated Article')
        ->and($article->description)->toBe('Updated Description');

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://api.myonlinestore.test/v1/articles/1' &&
            $request->method() === 'PATCH';
    });
});

it('can delete an article', function () {
    Http::fake([
        'api.myonlinestore.test/v1/articles/1' => Http::response(null, 204),
    ]);

    $result = $this->client->deleteArticle(1);

    expect($result)->toBeTrue();

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://api.myonlinestore.test/v1/articles/1' &&
            $request->method() === 'DELETE';
    });
});

it('can get articles count', function () {
    Http::fake([
        'api.myonlinestore.test/v1/articles/count' => Http::response([
            'count' => 42
        ]),
    ]);

    $count = $this->client->getArticlesCount();

    expect($count)->toBe(42);

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://api.myonlinestore.test/v1/articles/count' &&
            $request->method() === 'GET';
    });
});

it('handles api errors correctly', function () {
    Http::fake([
        'api.myonlinestore.test/v1/articles*' => Http::response([
            'error' => 'Invalid API credentials'
        ], 401),
    ]);

    expect(fn() => $this->client->getArticles())
        ->toThrow(YourNamespace\MyOnlineStore\Exceptions\AuthenticationException::class);
}); 