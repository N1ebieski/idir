<?php

namespace N1ebieski\IDir\Utils\Thumbnail;

use Illuminate\Support\Carbon;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Filesystem\Factory as Storage;

class ThumbnailUtil
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
     * Undocumented variable
     *
     * @var GuzzleResponse
     */
    protected $response;

    /**
     * Undocumented variable
     *
     * @var int
     */
    protected int $checkDays;

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected string $thumbnailUrl;

    /**
     * Undocumented variable
     *
     * @var string|null
     */
    protected ?string $thumbnailReloadUrl;

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

        $this->url = $url;

        $this->checkDays = $config->get('idir.dir.thumbnail.cache.days');
        $this->thumbnailUrl = $config->get('idir.dir.thumbnail.url');
        $this->thumbnailReloadUrl = $config->get('idir.dir.thumbnail.reload_url');

        $this->makeHost();
        $this->makeFilePath();
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function makeHost() : string
    {
        return $this->host = parse_url($this->url, PHP_URL_HOST);
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function makeFilePath() : string
    {
        $hash = md5($this->host);
        $path = implode('/', array_slice(str_split($hash), 0, 3));

        $this->file_path = $this->path . '/' . $path . '/' . $hash .'.jpg';

        return $this->file_path;
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

        return $this->carbon->parse($modified_at)
            ->addDays($this->checkDays) < $this->carbon->now();
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
     * @return GuzzleResponse
     */
    public function makeResponse(string $value) : GuzzleResponse
    {
        try {
            $this->response = $this->guzzle->request('GET', $value);
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            throw new \N1ebieski\IDir\Exceptions\Thumbnail\Exception(
                $e->getMessage(),
                $e->getCode()
            );
        }

        return $this->response;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function prepareResponse() : string
    {
        return $this->response = $this->response->getBody()->getContents();
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function generate() : string
    {
        if ($this->isReload()) {
            $this->makeResponse($this->thumbnailUrl . $this->url);
            $this->prepareResponse();

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
        if ($this->thumbnailReloadUrl !== null) {
            $this->makeResponse($this->thumbnailReloadUrl . $this->url);
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
        return $this->storage->disk('public')->put($this->file_path, $this->response);
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

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getLastModified() : string
    {
        return $this->storage->disk('public')->lastModified($this->file_path);
    }
}
