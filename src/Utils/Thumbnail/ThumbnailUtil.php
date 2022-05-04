<?php

namespace N1ebieski\IDir\Utils\Thumbnail;

use Illuminate\Support\Carbon;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Filesystem\Factory as Storage;
use N1ebieski\IDir\Http\Clients\Thumbnail\Provider\ThumbnailClient;

class ThumbnailUtil
{
    /**
     * Undocumented variable
     *
     * @var ThumbnailClient
     */
    public $client;

    /**
     * Undocumented variable
     *
     * @var Storage
     */
    protected $storage;

    /**
     * Undocumented variable
     *
     * @var Carbon
     */
    protected $carbon;

    /**
     * Undocumented variable
     *
     * @var Config
     */
    protected $config;

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $url;

    /**
     * [protected description]
     * @var string
     */
    protected $path = 'vendor/idir/thumbnails';

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $disk;

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
        ThumbnailClient $client,
        Storage $storage,
        Carbon $carbon,
        Config $config,
        string $url,
        string $disk = 'public'
    ) {
        $this->client = $client;
        $this->storage = $storage;
        $this->carbon = $carbon;
        $this->config = $config;

        $this->url = $url;
        $this->disk = $disk;
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @return static
     */
    public function make(string $url)
    {
        return new static($this->client, $this->storage, $this->carbon, $this->config, $url, $this->disk);
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function getHost(): string
    {
        return parse_url($this->url, PHP_URL_HOST);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function getFilePath()
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
     * @return string
     */
    public function generate(): string
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
