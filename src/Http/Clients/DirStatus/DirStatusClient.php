<?php

namespace N1ebieski\IDir\Http\Clients\DirStatus;

use Psr\Http\Message\ResponseInterface;
use Illuminate\Contracts\Container\Container as App;
use N1ebieski\IDir\Http\Clients\DirStatus\Requests\ShowRequest;

class DirStatusClient
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
     * @return ResponseInterface
     */
    public function show(string $url): ResponseInterface
    {
        return $this->app->make(ShowRequest::class, [
            'url' => $url
        ])();
    }
}
