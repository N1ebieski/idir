<?php

namespace N1ebieski\IDir\Utils;

use Illuminate\Support\Carbon;
use GuzzleHttp\Client as GuzzleClient;
use N1ebieski\ICore\Utils\Traits\Factory;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Filesystem\Factory as Storage;

class ThumbnailUtil
{
    use Factory;

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
     * @var GuzzleClient
     */
    protected $guzzle;

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
    protected $disk;

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $contents;

    /**
     * Undocumented function
     *
     * @param GuzzleClient $guzzle
     * @param Storage $storage
     * @param Carbon $carbon
     * @param Config $config
     * @param string $url
     * @param string $disk
     */
    public function __construct(
        GuzzleClient $guzzle,
        Storage $storage,
        Carbon $carbon,
        Config $config,
        string $url,        
        string $disk = 'public'
    ) {
        $this->guzzle = $guzzle;
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
     * @param GuzzleResponse $response
     * @return static
     */
    protected function setContentsFromResponse(GuzzleResponse $response)
    {
        $this->contents = $response->getBody()->getContents();

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
     * @param string $uri
     * @return GuzzleResponse
     */
    public function makeResponse(string $uri): GuzzleResponse
    {
        try {
            $response = $this->guzzle->request('GET', $uri);
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            throw new \N1ebieski\IDir\Exceptions\Thumbnail\Exception(
                $e->getMessage(),
                $e->getCode()
            );
        }

        return $response;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function generate(): string
    {
        if ($this->isReload()) {
            $this->setContentsFromResponse(
                $this->makeResponse($this->config->get('idir.dir.thumbnail.url') . $this->url)
            );

            $this->put();
        }

        return $this->get();
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function reload(): bool
    {
        if ($this->config->get('idir.dir.thumbnail.reload_url') !== null) {
            $this->setContentsFromResponse(
                $this->makeResponse($this->config->get('idir.dir.thumbnail.reload_url') . $this->url)
            );
        }

        if ($this->isExists()) {
            $this->delete();
        }

        return true;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function delete(): bool
    {
        return $this->storage->disk($this->disk)->delete($this->file_path);
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function put(): string
    {
        return $this->storage->disk($this->disk)->put($this->file_path, $this->contents);
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function get(): string
    {
        return $this->storage->disk($this->disk)->get($this->file_path);
    }
}
