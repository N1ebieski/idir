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

namespace N1ebieski\IDir\Http\Clients\Thumbnail;

use Psr\Http\Message\ResponseInterface;
use Illuminate\Contracts\Container\Container as App;
use N1ebieski\IDir\Http\Clients\Thumbnail\Requests\ReloadRequest;

class ThumbnailClient
{
    /**
     * Undocumented function
     *
     * @param App $app
     */
    public function __construct(protected App $app)
    {
        //
    }

    /**
     * Undocumented function
     *
     * @param array $parameters
     * @return ResponseInterface
     */
    public function reload(array $parameters)
    {
        /**
         * @var ReloadRequest
         */
        $request = $this->app->make(ReloadRequest::class, [
            'parameters' => $parameters
        ]);

        return $request->makeRequest();
    }
}
