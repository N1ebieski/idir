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

namespace N1ebieski\IDir\Http\Clients\Thumbnail\PagePeekerV3\Requests;

use Psr\Http\Message\ResponseInterface;
use N1ebieski\IDir\Http\Clients\Thumbnail\Provider\Requests\Request;

class ReloadRequest extends Request
{
    /**
     * @var string
     */
    protected $method = 'GET';

    /**
     * @return ResponseInterface
     * @throws \N1ebieski\IDir\Exceptions\Thumbnail\Exception
     */
    public function makeRequest(): ResponseInterface
    {
        try {
            return $this->client->request(
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
    }

    /**
     * Build the PagePeeker V3 signed thumbnail URL and force a fresh render.
     *
     * @return string
     */
    public function getThumbnailUrl(): string
    {
        $query = http_build_query([
            'url' => (string)$this->get('url'),
            'width' => $this->config->get('idir.dir.thumbnail.pagepeeker.width'),
            'height' => $this->config->get('idir.dir.thumbnail.pagepeeker.height'),
            'quality' => $this->config->get('idir.dir.thumbnail.pagepeeker.quality'),
            'fresh' => true,
            'key_id' => substr((string)$this->config->get('services.pagepeeker_v3.api_key'), 0, 8)
        ]);

        $signature = hash_hmac(
            'sha256',
            $query,
            (string)$this->config->get('services.pagepeeker_v3.api_secret')
        );

        return $this->config->get('idir.dir.thumbnail.reload_url') . '?' . $query . '&signature=' . $signature;
    }
}
