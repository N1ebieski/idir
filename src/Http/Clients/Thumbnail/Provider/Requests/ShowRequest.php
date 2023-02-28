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

namespace N1ebieski\IDir\Http\Clients\Thumbnail\Provider\Requests;

use Psr\Http\Message\ResponseInterface;
use N1ebieski\IDir\Http\Clients\Thumbnail\Provider\Requests\Request;

class ShowRequest extends Request
{
    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $method = 'GET';

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
                $this->getOptions()
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
        $thumbnailUrl = $this->config->get('idir.dir.thumbnail.url');

        if (strpos($thumbnailUrl, '{url}') === false) {
            $thumbnailUrl .= '{url}';
        }

        return str_replace('{url}', $this->get('url'), $thumbnailUrl);
    }
}
