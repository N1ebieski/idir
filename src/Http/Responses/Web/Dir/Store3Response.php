<?php

namespace N1ebieski\IDir\Http\Responses\Web\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Http\RedirectResponse;

/**
 * [Store3Response description]
 */
class Store3Response
{
    /**
     * [private description]
     * @var Dir
     */
    protected $dir;

    /**
     * [private description]
     * @var ResponseFactory
     */
    protected $response;

    /**
     * [private description]
     * @var Config
     */
    protected $config;

    /**
     * @param ResponseFactory $response
     * @param Config          $config
     */
    public function __construct(ResponseFactory $response, Config $config)
    {
        $this->response = $response;
        $this->config = $config;
    }

    /**
     * @param Dir $dir
     *
     * @return static
     */
    public function setDir(Dir $dir)
    {
        $this->dir = $dir;

        return $this;
    }

    /**
     * [response description]
     * @return RedirectResponse [description]
     */
    public function makeResponse() : RedirectResponse
    {
        switch ($this->dir->status) {
            case 0:
                return $this->response->redirectToRoute('web.dir.create_1')
                    ->with('success', trans('idir::dirs.success.store.status_0'));
            case 1:
                return $this->response->redirectToRoute('web.dir.show', [$this->dir->slug])
                    ->with('success', trans('idir::dirs.success.store.status_1'));
            case 2:
                return $this->response->redirectToRoute('web.payment.dir.show', [$this->dir->getPayment()->id]);
        }
    }
}
