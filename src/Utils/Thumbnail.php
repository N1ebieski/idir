<?php

namespace N1ebieski\IDir\Utils;

use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Contracts\Filesystem\Factory as Storage;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Support\Carbon;

class Thumbnail
{
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
    protected string $host;

    /**
     * [protected description]
     * @var string
     */
    protected string $path = 'vendor/idir/thumbnails';

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected string $file_path;

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
     * Undocumented function
     *
     * @param string $url
     * @param GuzzleClient $guzzle
     * @param Storage $storage
     * @param Carbon $carbon
     * @param Config $config
     */
    public function __construct(
        GuzzleClient $guzzle,
        Storage $storage,
        Carbon $carbon,
        Config $config,
        string $url
    ) {
        $this->guzzle = $guzzle;
        $this->storage = $storage;
        $this->carbon = $carbon;
        $this->config = $config;

        $this->url = $url;
        $this->host = $this->makeHost();
        $this->file_path = $this->makeFilePath();
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function makeHost() : string
    {
        return parse_url($this->url, PHP_URL_HOST);
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function makeFilePath() : string
    {
        $hash = md5($this->host);

        return $this->path . '/' . implode('/', array_slice(str_split($hash), 0, 3)). '/' . $hash .'.jpg';
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isReload() : bool
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
    protected function isTimeToReload() : bool
    {
        $modified_at = $this->storage->disk('public')->lastModified($this->file_path);

        return $this->carbon->parse($modified_at)->addDays(
            $this->config->get('idir.dir.thumbnail.cache.days')
        ) < $this->carbon->now();
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function isExists() : bool
    {
        return $this->storage->disk('public')->exists($this->file_path);
    }

    /**
     * Undocumented function
     *
     * @param string $value
     * @return string
     */
    protected function response(string $value) : string
    {
        try {
            $response = $this->guzzle->request('GET', $value);
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            throw new \N1ebieski\IDir\Exceptions\Thumbnail\Exception(
                $e->getMessage(),
                $e->getCode()
            );
        }

        return $response->getBody()->getContents();
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function generate() : string
    {
        if ($this->isReload()) {
            $this->put();
        }

        return $this->get();
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function reload() : bool
    {
        if ($this->config->has('idir.dir.thumbnail.reload_url')) {
            $this->response($this->config->get('idir.dir.thumbnail.reload_url') . $this->url);
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
    protected function delete() : bool
    {
        return $this->storage->disk('public')->delete($this->file_path);
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function put() : string
    {
        return $this->storage->disk('public')->put(
            $this->file_path,
            $this->response($this->config->get('idir.dir.thumbnail.url') . $this->url)
        );
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function get() : string
    {
        return $this->storage->disk('public')->get($this->file_path);
    }
}
