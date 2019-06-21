<?php

namespace TheBugSoftware\Teamwork;

use GuzzleHttp\Client;
use TheBugSoftware\Teamwork\Services\Desk;
use TheBugSoftware\Teamwork\Services\Tickets;
use TheBugSoftware\Teamwork\Services\HelpDocs;

class Teamwork
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * ApiClient constructor.
     */
    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => sprintf('https://%s.teamwork.com/desk/v1/', config('teamwork.desk.domain')),
            'auth'     => [config('teamwork.desk.key'), ''],
        ]);
    }

    /**
     * Teamwork Desk.
     *
     * @return Desk
     */
    public function desk(): Desk
    {
        return new Desk($this->client);
    }

    /**
     * Teamwork HelpDocs.
     *
     * @return HelpDocs
     */
    public function helpDocs(): HelpDocs
    {
        return new HelpDocs($this->client);
    }

    /**
     * Teamwork Tickets.
     *
     * @return Tickets
     */
    public function tickets(): Tickets
    {
        return new Tickets($this->client);
    }
}
