<?php

namespace TheBugSoftware\Teamwork\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use TheBugSoftware\Teamwork\Exceptions\TeamworkHttpException;
use TheBugSoftware\Teamwork\Exceptions\TeamworkParameterException;

class Tickets
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get tickets priorities.
     *
     * @return array
     * @throws TeamworkHttpException
     */
    public function priorities(): array
    {
        try {
            $response = $this->client->get('ticketpriorities.json');
            $body     = $response->getBody();

            return json_decode($body->getContents(), true);
        } catch (ClientException $e) {
            throw new TeamworkHttpException($e->getMessage(), 400);
        }
    }

    /**
     * Get a list of tickets for a customer.
     *
     * @param int $customerId
     * @return array
     * @throws TeamworkHttpException
     */
    public function customer(int $customerId): array
    {
        try {
            $response = $this->client->get(sprintf('customers/%s/previoustickets.json', $customerId));
            $body     = $response->getBody();

            return json_decode($body->getContents(), true);
        } catch (ClientException $e) {
            throw new TeamworkHttpException($e->getMessage(), 400);
        }
    }

    /**
     * Send a ticket to teamwork desk.
     *
     * @param array $data
     * @return array
     * @throws TeamworkHttpException
     */
    public function post(array $data): array
    {
        try {
            $response = $this->client->post('tickets.json', [
                'form_params' => $data,
            ]);

            $body = $response->getBody();

            return json_decode($body->getContents(), true);
        } catch (ClientException $e) {
            throw new TeamworkHttpException($e->getMessage(), 400);
        }
    }

    /**
     * Post a reply to a ticket.
     *
     * @param array $data
     * @return array
     * @throws TeamworkHttpException
     * @throws TeamworkParameterException
     */
    public function reply(array $data): array
    {
        if (empty($data['ticketId'])) {
            throw new TeamworkParameterException('The `reply` method expects the passed array param to contain `ticketId`', 400);
        }

        try {
            $response = $this->client->post(sprintf('tickets/%s.json', $data['ticketId']), [
                'form_params' => $data,
            ]);

            $body = $response->getBody();

            return json_decode($body->getContents(), true);
        } catch (ClientException $e) {
            throw new TeamworkHttpException($e->getMessage());
        }
    }

    /**
     * Get ticket by id.
     *
     * @param int $ticketId
     * @return array
     * @throws TeamworkHttpException
     */
    public function ticket(int $ticketId): array
    {
        try {
            $response = $this->client->get(sprintf('tickets/%s.json', $ticketId));
            $body     = $response->getBody();

            return json_decode($body->getContents(), true);
        } catch (ClientException $e) {
            throw new TeamworkHttpException($e->getMessage(), 400);
        }
    }
}
