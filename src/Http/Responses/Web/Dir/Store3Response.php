<?php

namespace N1ebieski\IDir\Http\Responses\Web\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Http\RedirectResponse;
use N1ebieski\IDir\ValueObjects\Dir\Status;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Routing\UrlGenerator as URL;
use Illuminate\Contracts\Translation\Translator as Lang;
use N1ebieski\IDir\Http\Responses\Web\Dir\RedirectResponseFactory;

class Store3Response implements RedirectResponseFactory
{
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
     * Undocumented variable
     *
     * @var URL
     */
    protected $url;

    /**
     * Undocumented function
     *
     * @param ResponseFactory $response
     * @param Config $config
     * @param Lang $lang
     * @param URL $url
     */
    public function __construct(ResponseFactory $response, Config $config, Lang $lang, URL $url)
    {
        $this->response = $response;
        $this->config = $config;
        $this->lang = $lang;
        $this->url = $url;
    }

    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @return RedirectResponse
     */
    public function makeResponse(Dir $dir): RedirectResponse
    {
        switch ($dir->status->getValue()) {
            case Status::INACTIVE:
                return $this->response->redirectToRoute('web.dir.create_1')
                    ->with('success', $this->lang->get('idir::dirs.success.store.' . Status::INACTIVE));
            case Status::ACTIVE:
                return $this->response->redirectToRoute('web.dir.show', [$dir->slug])
                    ->with('success', $this->lang->get('idir::dirs.success.store.' . Status::ACTIVE));
            case Status::PAYMENT_INACTIVE:
                return $this->response->redirectToRoute('web.payment.dir.show', [
                    $dir->payment->uuid,
                    $dir->payment->driver
                ]);
        }
    }
}
