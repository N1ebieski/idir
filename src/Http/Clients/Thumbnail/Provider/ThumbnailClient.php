<?php

namespace N1ebieski\IDir\Http\Clients\Thumbnail\Provider;

use Psr\Http\Message\ResponseInterface;
use Illuminate\Contracts\Container\Container as App;
use N1ebieski\IDir\Http\Clients\Thumbnail\Provider\Requests\ShowRequest;
use N1ebieski\IDir\Http\Clients\Thumbnail\Provider\Requests\ReloadRequest;
use N1ebieski\IDir\Http\Clients\Thumbnail\Provider\Responses\ShowResponse;

class ThumbnailClient
{
    /**
     * Undocumented variable
     *
     * @var App
     */
    protected $app;

    /**
     * Undocumented function
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * Undocumented function
     *
     * @param array $parameters
     * @return ResponseInterface
     */
    public function show(array $parameters): ResponseInterface
    {
        return $this->app->make(ShowRequest::class, [
            'parameters' => $parameters
        ])();
    }

    /**
     * Undocumented function
     *
     * @param array $parameters
     * @return ResponseInterface
     */
    public function reload(array $parameters)
    {
        return $this->app->make(ReloadRequest::class, [
            'parameters' => $parameters
        ])();
    }
}
