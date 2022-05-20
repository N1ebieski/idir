<?php

namespace N1ebieski\IDir\Http\Clients\DirBacklink;

use Psr\Http\Message\ResponseInterface;
use Illuminate\Contracts\Container\Container as App;
use N1ebieski\IDir\Http\Clients\DirBacklink\Requests\ShowRequest;

class DirBacklinkClient
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
     * @param string $url
     * @return  ResponseInterface
     */
    public function show(string $url): ResponseInterface
    {
        /**
         * @var ShowRequest
         */
        $request = $this->app->make(ShowRequest::class, [
            'url' => $url
        ]);

        return $request->makeRequest();
    }
}
