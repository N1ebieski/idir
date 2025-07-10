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

use Illuminate\Support\Carbon;
use N1ebieski\IDir\Jobs\Thumbnail\GenerateJob;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Filesystem\Factory as Storage;
use N1ebieski\IDir\Overrides\Illuminate\Contracts\Bus\Dispatcher;
use N1ebieski\IDir\Utils\Thumbnail\Interfaces\ThumbnailInterface;
use N1ebieski\IDir\Http\Clients\Thumbnail\Provider\ThumbnailClient;

class LocalThumbnail implements ThumbnailInterface
{
    protected string $path = 'vendor/idir/thumbnails';

    public function __construct(
        protected ThumbnailClient $client,
        protected Storage $storage,
        protected Dispatcher $busDispatcher,
        protected Carbon $carbon,
        protected Config $config,
        protected string $url,
        protected string $disk = 'public',
    ) {
        //
    }

    public function make(string $url): self
    {
        return new self($this->client, $this->storage, $this->busDispatcher, $this->carbon, $this->config, $url, $this->disk);
    }

    protected function getHost(): string
    {
        return parse_url($this->url, PHP_URL_HOST) ?: '';
    }

    public function getFilePath(): string
    {
        $hash = md5($this->getHost());
        $path = implode('/', array_slice(str_split($hash), 0, 3));

        return $this->path . '/' . $path . '/' . $hash . '.jpg';
    }

    public function getLastModified(): string
    {
        /** @var string */
        return $this->storage->disk($this->disk)->lastModified($this->getFilePath());
    }

    public function isReload(): bool
    {
        if (!$this->isExists()) {
            return true;
        }

        if ($this->isTimeToReload()) {
            return true;
        }

        return false;
    }

    protected function isTimeToReload(): bool
    {
        /** @var string */
        $modified_at = $this->storage->disk($this->disk)->lastModified($this->getFilePath());

        return $this->carbon->parse($modified_at)
            ->addDays($this->config->get('idir.dir.thumbnail.cache.days'))
            ->lessThan($this->carbon->now());
    }

    protected function isExists(): bool
    {
        return $this->storage->disk($this->disk)->exists($this->getFilePath());
    }

    public function generate(): bool|string|null
    {
        if ($this->isReload()) {
            $this->busDispatcher->dispatch(new GenerateJob($this->url, $this->disk));

            return true;
        }

        return $this->storage->disk($this->disk)->get($this->getFilePath());
    }

    public function reload(): bool
    {
        $this->busDispatcher->dispatch(new GenerateJob($this->url, $this->disk));

        if ($this->isExists()) {
            $this->storage->disk($this->disk)->delete($this->getFilePath());
        }

        return true;
    }
}
