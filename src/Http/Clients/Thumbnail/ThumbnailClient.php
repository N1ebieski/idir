<?php

namespace N1ebieski\IDir\Http\Clients\Thumbnail;

use Psr\Http\Message\ResponseInterface;
use Illuminate\Contracts\Container\Container as App;
use N1ebieski\IDir\Http\Clients\Thumbnail\Requests\ReloadRequest;

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
    public function reload(array $parameters)
    {
        /**
         * @var ReloadRequest
         */
        $request = $this->app->make(ReloadRequest::class, [
            'parameters' => $parameters
        ]);

        return $request->makeRequest();
    }
}
