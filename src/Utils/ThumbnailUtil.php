<?php

namespace N1ebieski\IDir\Utils;

use Illuminate\Support\Carbon;
use N1ebieski\ICore\Utils\Traits\Factory;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Filesystem\Factory as Storage;
use N1ebieski\IDir\Http\Clients\Thumbnail\Provider\Client;

class ThumbnailUtil
{
    use Factory;

    /**
     * Undocumented variable
     *
     * @var Client
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
     * Undocumented variable
     *
     * @var string
     */
    protected $host;

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
    protected $file_path;

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $disk;

    /**
     * Undocumented function
     *
     * @param Client $client
     * @param Storage $storage
     * @param Carbon $carbon
     * @param Config $config
     * @param string $url
     * @param string $disk
     */
    public function __construct(
        Client $client,
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

        if (!empty($this->url)) {
            $this->setHostFromUrl($this->url);
            $this->setFilePathFromHost($this->host);
        }
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @return static
     */
    protected function setHostFromUrl(string $url)
    {
        $this->host = parse_url($url, PHP_URL_HOST);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $host
     * @return static
     */
    protected function setFilePathFromHost(string $host)
    {
        $hash = md5($host);
        $path = implode('/', array_slice(str_split($hash), 0, 3));

        $this->file_path = $this->path . '/' . $path . '/' . $hash . '.jpg';

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getLastModified(): string
    {
        return $this->storage->disk($this->disk)->lastModified($this->file_path);
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
        $modified_at = $this->storage->disk($this->disk)->lastModified($this->file_path);

        return $this->carbon->parse($modified_at)
            ->addDays($this->config->get('idir.dir.thumbnail.cache.days')) < $this->carbon->now();
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function isExists(): bool
    {
        return $this->storage->disk($this->disk)->exists($this->file_path);
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
                $this->file_path,
                $this->client->get($this->config->get('idir.dir.thumbnail.url'), [$this->url])
            );
        }

        return $this->storage->disk($this->disk)->get($this->file_path);
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function reload(): bool
    {
        $this->client->get($this->config->get('idir.dir.thumbnail.reload_url'), [$this->url]);

        if ($this->isExists()) {
            $this->storage->disk($this->disk)->delete($this->file_path);
        }

        return true;
    }
}
