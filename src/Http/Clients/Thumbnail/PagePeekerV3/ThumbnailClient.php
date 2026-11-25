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

namespace N1ebieski\IDir\Http\Clients\Thumbnail\PagePeekerV3;

use Psr\Http\Message\ResponseInterface;
use Illuminate\Contracts\Container\Container as App;
use N1ebieski\IDir\Http\Clients\Thumbnail\PagePeekerV3\Requests\ShowRequest;
use N1ebieski\IDir\Http\Clients\Thumbnail\Interfaces\ThumbnailClientInterface;
use N1ebieski\IDir\Http\Clients\Thumbnail\PagePeekerV3\Requests\ReloadRequest;

class ThumbnailClient implements ThumbnailClientInterface
{
    public function __construct(protected App $app)
    {
        //
    }

    public function show(array $parameters): ResponseInterface
    {
        /** @var ShowRequest $request */
        $request = $this->app->make(ShowRequest::class, [
            'parameters' => $parameters
        ]);

        return $request->makeRequest();
    }

    public function reload(array $parameters): ResponseInterface
    {
        /** @var ReloadRequest $request */
        $request = $this->app->make(ReloadRequest::class, [
            'parameters' => $parameters
        ]);

        return $request->makeRequest();
    }
}
