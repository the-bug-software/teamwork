<?php

namespace TheBugSoftware\Teamwork\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Pool as GuzzlePool;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use TheBugSoftware\Teamwork\Exceptions\TeamworkHttpException;

class HelpDocs
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get HelpDocs sites.
     *
     * @return array
     * @throws TeamworkHttpException
     */
    public function getSites(): array
    {
        try {
            $response = $this->client->get('helpdocs/sites.json');
            $body     = $response->getBody();

            return json_decode($body->getContents(), true);
        } catch (ClientException $e) {
            throw new TeamworkHttpException($e->getMessage(), 400);
        }
    }

    /**
     * Get HelpDocs site.
     *
     * @param int $siteID
     *
     * @return array
     * @throws TeamworkHttpException
     */
    public function getSite(int $siteID): array
    {
        try {
            $response = $this->client->get(sprintf('helpdocs/sites/%s.json', $siteID));
            $body     = $response->getBody();

            return json_decode($body->getContents(), true);
        } catch (ClientException $e) {
            throw new TeamworkHttpException($e->getMessage(), 400);
        }
    }

    /**
     * Get articles within a category.
     *
     * @param int $categoryID
     * @param int $page
     *
     * @return array
     * @throws TeamworkHttpException
     */
    public function getCategoryArticles(int $categoryID, int $page = 1): array
    {
        try {
            $response = $this->client->get(sprintf('helpdocs/categories/%s/articles.json', $categoryID), [
                'query' => compact('page'),
            ]);

            $body = $response->getBody();

            return json_decode($body->getContents(), true);
        } catch (ClientException $e) {
            throw new TeamworkHttpException($e->getMessage(), 400);
        }
    }

    /**
     * Get articles within a site.
     *
     * @param int $siteID
     * @param int $page
     *
     * @return array
     * @throws TeamworkHttpException
     */
    public function getSiteArticles(int $siteID, int $page = 1): array
    {
        try {
            $response = $this->client->get(sprintf('helpdocs/sites/%s/articles.json', $siteID), [
                'query' => compact('page'),
            ]);
            $body     = $response->getBody();

            return json_decode($body->getContents(), true);
        } catch (ClientException $e) {
            throw new TeamworkHttpException($e->getMessage(), 400);
        }
    }

    /**
     * Get article by id.
     *
     * @param int $articleID
     *
     * @return array
     * @throws TeamworkHttpException
     */
    public function getArticle(int $articleID): array
    {
        try {
            $response = $this->client->get(sprintf('helpdocs/articles/%s.json', $articleID));
            $body     = $response->getBody();

            return json_decode($body->getContents(), true);
        } catch (ClientException $e) {
            throw new TeamworkHttpException($e->getMessage(), 400);
        }
    }

    /**
     * Get articles (in bulk).
     *
     * @param array $articleIDs
     *
     * @return array
     */
    public function getArticles(array $articleIDs): array
    {
        $articles = [];

        $requests = array_map(function ($articleID) {
            return new GuzzleRequest('GET', sprintf('helpdocs/articles/%s.json', $articleID));
        }, $articleIDs);

        $pool = new GuzzlePool($this->client, $requests, [
            'concurrency' => 10,
            'fulfilled'   => function ($response) use (&$articles) {
                $response = json_decode($response->getBody(), true);

                $articles[] = $response['article'];
            },
        ]);

        $promise = $pool->promise();
        $promise->wait();

        return $articles;
    }

    /**
     * Get categories within a site.
     *
     * @param int $siteID
     *
     * @return array
     * @throws TeamworkHttpException
     */
    public function getSiteCategories(int $siteID): array
    {
        try {
            $response = $this->client->get(sprintf('helpdocs/sites/%s/categories.json', $siteID));
            $body     = $response->getBody();

            return json_decode($body->getContents(), true);
        } catch (ClientException $e) {
            throw new TeamworkHttpException($e->getMessage(), 400);
        }
    }
}
