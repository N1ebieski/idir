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

namespace N1ebieski\IDir\Http\Clients\DirBacklink\Requests;

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
     * @var array
     */
    protected $options = [
        'verify' => false
    ];

    /**
     * Undocumented function
     *
     * @param string $url
     * @param ClientInterface $client
     */
    public function __construct(
        protected string $url,
        protected ClientInterface $client
    ) {
        //
    }

    /**
     *
     * @return string
     */
    public function getUrlAsAscii(): string
    {
        /** @var array */
        $parts = parse_url($this->url);

        // @phpstan-ignore-next-line
        return str_replace($parts['host'], idn_to_ascii($parts['host']), $this->url);
    }

    /**
     * Undocumented function
     *
     * @return ResponseInterface
     * @throws \N1ebieski\IDir\Exceptions\DirBacklink\TransferException
     */
    public function makeRequest(): ResponseInterface
    {
        try {
            $response = $this->client->request(
                $this->method,
                $this->getUrlAsAscii(),
                $this->options
            );
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            throw new \N1ebieski\IDir\Exceptions\DirBacklink\TransferException(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }

        return $response;
    }
}
