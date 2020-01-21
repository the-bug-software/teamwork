<?php

namespace TheBugSoftware\Teamwork\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\File;
use TheBugSoftware\Teamwork\Exceptions\TeamworkHttpException;
use TheBugSoftware\Teamwork\Exceptions\TeamworkInboxException;
use TheBugSoftware\Teamwork\Exceptions\TeamworkUploadException;

class Desk
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Return an inbox by name.
     *
     * @param string $name
     *
     * @return array
     * @throws TeamworkHttpException
     * @throws TeamworkInboxException
     */
    public function inbox(string $name): array
    {
        try {
            $response = $this->client->get('inboxes.json');
            $body     = $response->getBody();
            $inboxes  = json_decode($body->getContents(), true);

            $inbox = collect($inboxes['inboxes'])->first(function ($inbox) use ($name) {
                return $inbox['name'] === $name;
            });

            if (!$inbox) {
                throw new TeamworkInboxException("No inbox found with the name: $name!", 400);
            }

            return $inbox;
        } catch (ClientException $e) {
            throw new TeamworkHttpException($e->getMessage(), 400);
        }
    }

    /**
     * Get teamwork desk inboxes.
     *
     * @return array
     * @throws TeamworkHttpException
     */
    public function inboxes(): array
    {
        try {
            $response = $this->client->get('inboxes.json');
            $body     = $response->getBody();

            return json_decode($body->getContents(), true);
        } catch (ClientException $e) {
            throw new TeamworkHttpException($e->getMessage(), 400);
        }
    }

    /**
     * Return the current client info.
     *
     * @return array
     * @throws TeamworkHttpException
     */
    public function me(): array
    {
        try {
            $response = $this->client->get('me.json');
            $body     = $response->getBody();

            return json_decode($body->getContents(), true);
        } catch (ClientException $e) {
            throw new TeamworkHttpException($e->getMessage(), 400);
        }
    }

    /**
     * Upload file to teamwork desk.
     *
     * @param $userId
     * @param $file
     *
     * @return array
     * @throws TeamworkHttpException
     * @throws TeamworkUploadException
     */
    public function upload($userId, $file): array
    {
        if (empty($file)) {
            throw new TeamworkUploadException('No file provided.', 400);
        }

        $filename  = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $path      = sys_get_temp_dir();
        $temp      = $file->move($path, $filename);
        $stream    = fopen($temp->getPathName(), 'r');

        try {
            $response = $this->client->post('upload/attachment', [
                'multipart' => [
                    [
                        'name'     => 'file',
                        'contents' => $stream,
                    ],
                    [
                        'name'     => 'userId',
                        'contents' => $userId,
                    ],
                ],
            ]);

            $body = $response->getBody();
            $body = json_decode($body->getContents(), true);

            if (!empty($stream)) {
                File::delete($temp->getPathName());
            }

            return [
                'id'        => $body['attachment']['id'],
                'url'       => $body['attachment']['downloadURL'],
                'extension' => $extension,
                'name'      => $body['attachment']['filename'],
                'size'      => $body['attachment']['size'],
            ];
        } catch (ClientException $e) {
            throw new TeamworkHttpException($e->getMessage(), 400);
        }
    }
}
