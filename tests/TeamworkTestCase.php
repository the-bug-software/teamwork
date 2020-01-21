<?php

namespace TheBugSoftware\Teamwork\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\UploadedFile;
use Orchestra\Testbench\TestCase;
use GuzzleHttp\Handler\MockHandler;
use TheBugSoftware\Teamwork\Teamwork;
use Illuminate\Support\Facades\Storage;

class TeamworkTestCase extends TestCase
{
    protected $app;

    /**
     * Define environment setup.
     *
     * @param Application $app
     */
    protected function getEnvironmentSetUp($app): void
    {
        // Setup the Teamwork domain and API Key
        $app['config']->set('teamwork.desk.domain', 'somedomain');
        $app['config']->set('teamwork.desk.key', '564asdas6ywudMIVvFCIegEGcvxLPq2800HfB49dHFiVyRe8FTKyP');

        $this->app      = $app;
    }

    protected function getUploadFileRequest($fileName, $multiple = false): Request
    {
        Storage::fake('avatars');

        if ($multiple) {
            $files = [
                $fileName => [
                    UploadedFile::fake()->image('image.jpg'),
                    UploadedFile::fake()->image('image2.jpg'),
                ],
            ];
        } else {
            $files = [$fileName => UploadedFile::fake()->image('image.jpg')];
        }

        return new Request(
            [],
            [],
            [],
            [],
            $files,
            ['CONTENT_TYPE' => 'application/json'],
            null
        );
    }

    protected function mockClient($status, $body): Client
    {
        $mock    = new MockHandler([new Response($status, [], $body)]);
        $handler = HandlerStack::create($mock);

        return new Client(['handler' => $handler]);
    }
}
