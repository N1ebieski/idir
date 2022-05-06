<?php

namespace N1ebieski\IDir\Http\Clients\Thumbnail\Provider\Requests;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use N1ebieski\ICore\Http\Clients\Request;
use Illuminate\Contracts\Config\Repository as Config;

class ReloadRequest extends Request
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
     * @var array
     */
    protected $options = [
        'verify' => false
    ];

    /**
     * Undocumented function
     *
     * @param array $parameters
     * @param ClientInterface $client
     * @param Config $config
     */
    public function __construct(
        array $parameters,
        ClientInterface $client,
        Config $config
    ) {
        $this->config = $config;

        parent::__construct($parameters, $client);
    }

    /**
     * Undocumented function
     *
     * @return ResponseInterface
     * @throws \N1ebieski\IDir\Exceptions\Thumbnail\Exception
     */
    public function makeRequest(): ResponseInterface
    {
        try {
            $response = $this->client->request(
                $this->method,
                $this->getThumbnailUrl(),
                $this->options
            );
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            throw new \N1ebieski\IDir\Exceptions\Thumbnail\Exception(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }

        return $response;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function getThumbnailUrl(): string
    {
        $thumbnailUrl = $this->config->get('idir.dir.thumbnail.reload_url');

        if (strpos($thumbnailUrl, '{url}') === false) {
            $thumbnailUrl .= '{url}';
        }

        return str_replace('{url}', $this->get('url'), $thumbnailUrl);
    }
}
