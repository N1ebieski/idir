<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\IDir\Http\Clients\Thumbnail\Requests;

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
    protected $method = 'PATCH';

    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $options = [
        'headers' => [
            'Accept' => 'application/json'
        ],
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
        protected Config $config
    ) {
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
                array_merge_recursive($this->options, [
                    'headers' => [
                        'Authorization' => $this->config->get('idir.dir.thumbnail.key'),
                    ]
                ])
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
        $thumbnailUrl = $this->config->get('idir.dir.thumbnail.api.reload_url');

        if (strpos($thumbnailUrl, '{url}') === false) {
            $thumbnailUrl .= '{url}';
        }

        return str_replace('{url}', $this->get('url'), $thumbnailUrl);
    }
}
