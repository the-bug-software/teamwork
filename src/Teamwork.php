<?php

namespace TheBugSoftware\Teamwork;

use GuzzleHttp\Client;
use TheBugSoftware\Teamwork\Services\Desk;
use TheBugSoftware\Teamwork\Services\HelpDocs;
use TheBugSoftware\Teamwork\Services\Tickets;

class Teamwork
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => sprintf('https://%s.teamwork.com/desk/v1/', config('teamwork.desk.domain')),
            'auth'     => [config('teamwork.desk.key'), ''],
        ]);
    }

    private function getClient(): Client
    {
        return $this->client;
    }

    public static function desk(): Desk
    {
        return new Desk((new Teamwork)->getClient());
    }

    public static function helpDocs(): HelpDocs
    {
        return new HelpDocs((new Teamwork)->getClient());
    }

    public static function tickets(): Tickets
    {
        return new Tickets((new Teamwork)->getClient());
    }
}
