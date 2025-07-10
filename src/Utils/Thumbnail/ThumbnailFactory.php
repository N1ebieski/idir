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

namespace N1ebieski\IDir\Utils\Thumbnail;

use Illuminate\Contracts\Container\Container;
use N1ebieski\IDir\ValueObjects\Thumbnail\Driver;
use N1ebieski\IDir\Utils\Thumbnail\Interfaces\ThumbnailInterface;

class ThumbnailFactory
{
    public function __construct(protected Container $app)
    {
    }

    public function make(Driver $driver): ThumbnailInterface
    {
        return match ($driver) {
            Driver::Local => $this->app->make(\N1ebieski\IDir\Utils\Thumbnail\LocalThumbnail::class),
            default => $this->app->make(\N1ebieski\IDir\Utils\Thumbnail\Thumbnail::class),
        };
    }
}
