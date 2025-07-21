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
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Filesystem\Factory as Storage;
use N1ebieski\IDir\Utils\Thumbnail\Interfaces\ThumbnailInterface;
use N1ebieski\IDir\Http\Clients\Thumbnail\Provider\ThumbnailClient;

class Thumbnail implements ThumbnailInterface
{
    /**
     * [protected description]
     * @var string
     */
    protected $path = 'vendor/idir/thumbnails';

    /**
     * Undocumented function
     *
     * @param ThumbnailClient $client
     * @param Storage $storage
     * @param Carbon $carbon
     * @param Config $config
     * @param string $url
     * @param string $disk
     */
    public function __construct(
        protected ThumbnailClient $client,
        protected Storage $storage,
        protected Carbon $carbon,
        protected Config $config,
        protected string $url,
        protected string $disk = 'public'
    ) {
        //
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @return self
     */
    public function make(string $url): self
    {
        return new self($this->client, $this->storage, $this->carbon, $this->config, $url, $this->disk);
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function getHost(): string
    {
        return parse_url($this->url, PHP_URL_HOST) ?: '';
    }

    /**
     *
     * @return string
     */
    public function getFilePath(): string
    {
        $hash = md5($this->getHost());
        $path = implode('/', array_slice(str_split($hash), 0, 3));

        return $this->path . '/' . $path . '/' . $hash . '.jpg';
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getLastModified(): string
    {
        /** @var string */
        return $this->storage->disk($this->disk)->lastModified($this->getFilePath());
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
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

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function isTimeToReload(): bool
    {
        /** @var string */
        $modified_at = $this->storage->disk($this->disk)->lastModified($this->getFilePath());

        return $this->carbon->parse($modified_at)
            ->addDays($this->config->get('idir.dir.thumbnail.cache.days'))
            ->lessThan($this->carbon->now());
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function isExists(): bool
    {
        return $this->storage->disk($this->disk)->exists($this->getFilePath());
    }

    /**
     * Undocumented function
     *
     * @return string|null
     */
    public function generate(): ?string
    {
        if ($this->isReload()) {
            $this->storage->disk($this->disk)->put(
                $this->getFilePath(),
                $this->client->show(['url' => $this->url])->getBody()->getContents()
            );
        }

        return $this->storage->disk($this->disk)->get($this->getFilePath());
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function reload(): bool
    {
        $this->client->reload(['url' => $this->url]);

        if ($this->isExists()) {
            $this->storage->disk($this->disk)->delete($this->getFilePath());
        }

        return true;
    }
}
