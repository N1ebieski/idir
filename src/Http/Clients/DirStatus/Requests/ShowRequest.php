<?php

namespace N1ebieski\IDir\Http\Clients\DirStatus\Requests;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;

class ShowRequest
{
    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $method = 'GET';

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $url;

    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $options = [
        'verify' => false,
        'allow_redirects' => [
            'track_redirects' => true
        ],
        'decode_content' => false,
        'http_errors' => true
    ];

    /**
     * Undocumented variable
     *
     * @var ClientInterface
     */
    protected $client;

    /**
     * Undocumented function
     *
     * @param string $url
     * @param ClientInterface $client
     */
    public function __construct(string $url, ClientInterface $client)
    {
        $this->url = $url;

        $this->client = $client;
    }

    /**
     * Undocumented function
     *
     * @return ResponseInterface
     * @throws \N1ebieski\IDir\Exceptions\DirStatus\TransferException
     */
    public function makeRequest(): ResponseInterface
    {
        try {
            $response = $this->client->request(
                $this->method,
                $this->url,
                $this->options
            );
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            throw new \N1ebieski\IDir\Exceptions\DirStatus\TransferException(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }

        return $response;
    }
}