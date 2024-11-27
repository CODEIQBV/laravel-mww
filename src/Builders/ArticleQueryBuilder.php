<?php

namespace YourNamespace\MyOnlineStore\Builders;

class ArticleQueryBuilder
{
    protected $client;
    protected $filters = [];

    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * Set the maximum number of items to return
     */
    public function limit(int $limit)
    {
        $this->filters['limit'] = min($limit, 100);
        return $this;
    }

    /**
     * Set the number of items to skip
     */
    public function offset(int $offset)
    {
        $this->filters['offset'] = $offset;
        return $this;
    }

    /**
     * Filter by creation date range
     */
    public function createdBetween(string $startDateTime, string $endDateTime)
    {
        $this->filters['created_start_date'] = $startDateTime;
        $this->filters['created_end_date'] = $endDateTime;
        return $this;
    }

    /**
     * Filter by last modified date range
     */
    public function changedBetween(string $startDateTime, string $endDateTime)
    {
        $this->filters['changed_start_date'] = $startDateTime;
        $this->filters['changed_end_date'] = $endDateTime;
        return $this;
    }

    /**
     * Filter by specific article IDs
     */
    public function byIds(array $ids)
    {
        $this->filters['ids'] = $ids;
        return $this;
    }

    /**
     * Filter by specific article UUIDs
     */
    public function byUuids(array $uuids)
    {
        $this->filters['uuids'] = $uuids;
        return $this;
    }

    /**
     * Set the language for the response
     */
    public function language(string $language)
    {
        $this->filters['language'] = $language;
        return $this;
    }

    /**
     * Get the articles
     */
    public function get(bool $raw = false)
    {
        return $this->client->getArticles($this->filters, $raw);
    }

    /**
     * Create a new article
     * 
     * @param CreateArticleData|array $articleData
     * @param bool $raw Whether to return raw response or use Resource
     * @return array|\YourNamespace\MyOnlineStore\Resources\ArticleResource
     */
    public function create(CreateArticleData|array $articleData, bool $raw = false)
    {
        return $this->client->createArticle($articleData, [
            'language' => $this->filters['language'] ?? null,
        ], $raw);
    }

    /**
     * Get a specific article by ID
     * 
     * @param int $articleId
     * @param bool $useUrlId Whether the ID is from article page URLs
     * @param bool $raw Whether to return raw response or use Resource
     * @return array|\YourNamespace\MyOnlineStore\Resources\ArticleResource
     */
    public function find(int $articleId, bool $useUrlId = false, bool $raw = false)
    {
        return $this->client->getArticle($articleId, [
            'language' => $this->filters['language'] ?? null,
            'use_url_id' => $useUrlId,
        ], $raw);
    }

    /**
     * Delete a specific article
     * 
     * @param int $articleId
     * @param bool $useUrlId Whether the ID is from article page URLs
     * @return bool Success status
     */
    public function delete(int $articleId, bool $useUrlId = false): bool
    {
        return $this->client->deleteArticle($articleId, [
            'language' => $this->filters['language'] ?? null,
            'use_url_id' => $useUrlId,
        ]);
    }

    /**
     * Update a specific article
     * 
     * @param int $articleId
     * @param UpdateArticleData|array $articleData
     * @param bool $useUrlId Whether the ID is from article page URLs
     * @param bool $raw Whether to return raw response or use Resource
     * @return array|\YourNamespace\MyOnlineStore\Resources\ArticleResource
     */
    public function update(
        int $articleId,
        UpdateArticleData|array $articleData,
        bool $useUrlId = false,
        bool $raw = false
    ) {
        return $this->client->updateArticle($articleId, $articleData, [
            'language' => $this->filters['language'] ?? null,
            'use_url_id' => $useUrlId,
        ], $raw);
    }

    /**
     * Delete an image from an article
     * 
     * @param int $imageId
     * @return bool Success status
     */
    public function deleteImage(int $imageId): bool
    {
        return $this->client->deleteArticleImage($imageId, [
            'language' => $this->filters['language'] ?? null,
        ]);
    }

    /**
     * Get the count of articles matching the current filters
     * 
     * @return int
     */
    public function count(): int
    {
        $filters = array_intersect_key(
            $this->filters,
            array_flip([
                'language',
                'created_start_date',
                'created_end_date',
                'changed_start_date',
                'changed_end_date',
            ])
        );

        return $this->client->getArticlesCount($filters);
    }
} 