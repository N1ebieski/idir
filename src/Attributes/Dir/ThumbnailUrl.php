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

namespace N1ebieski\IDir\Attributes\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Casts\Attribute;
use N1ebieski\IDir\ValueObjects\Thumbnail\Driver;
use N1ebieski\IDir\Http\Clients\Thumbnail\PagePeekerV3\Requests\ShowRequest;

class ThumbnailUrl
{
    /**
     *
     * @param Dir $dir
     * @return void
     */
    public function __construct(protected Dir $dir)
    {
        //
    }

    /**
     *
     * @return Attribute
     */
    public function __invoke(): Attribute
    {
        return new Attribute(
            get: function (): string {
                if (Config::get('idir.dir.thumbnail.cache.url') && Config::get('idir.dir.thumbnail.key')) {
                    return $this->dir->makeCache()->rememberThumbnailUrl();
                }

                /** @var Driver $driver */
                $driver = Config::get('idir.dir.thumbnail.driver');

                if ($driver->isEquals(Driver::PagePeekerV3)) {
                    /** @var ShowRequest $showRequest */
                    $showRequest = App::make(ShowRequest::class, [
                        'parameters' => [
                            'url' => $this->dir->url
                        ]
                    ]);

                    return $showRequest->getThumbnailUrl();
                }

                return Config::get('idir.dir.thumbnail.url') . $this->dir->url;
            }
        );
    }
}
