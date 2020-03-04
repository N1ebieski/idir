<?php

namespace N1ebieski\IDir\Http\Responses\Web\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Translation\Translator as Lang;
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
     * Undocumented variable
     *
     * @var Lang
     */
    protected $lang;

    /**
     * @param ResponseFactory $response
     * @param Config          $config
     * @param Lang            $lang
     */
    public function __construct(ResponseFactory $response, Config $config, Lang $lang)
    {
        $this->response = $response;
        $this->config = $config;
        $this->lang = $lang;
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
            case Dir::INACTIVE:
                return $this->response->redirectToRoute('web.dir.create_1')
                    ->with('success', $this->lang->get('idir::dirs.success.store.status_0'));
            case Dir::ACTIVE:
                return $this->response->redirectToRoute('web.dir.show', [$this->dir->slug])
                    ->with('success', $this->lang->get('idir::dirs.success.store.status_1'));
            case Dir::PAYMENT_INACTIVE:
                return $this->response->redirectToRoute('web.payment.dir.show', [$this->dir->getPayment()->uuid]);
        }
    }
}
