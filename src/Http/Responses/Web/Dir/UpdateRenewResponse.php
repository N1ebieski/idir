<?php

namespace N1ebieski\IDir\Http\Responses\Web\Dir;

use Illuminate\Http\Request;
use N1ebieski\IDir\Models\Dir;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Translation\Translator as Lang;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Routing\UrlGenerator as URL;
use N1ebieski\IDir\Http\Responses\RedirectResponseFactory;

/**
 * [UpdateRenewResponse description]
 */
class UpdateRenewResponse implements RedirectResponseFactory
{
    /**
     * [private description]
     * @var Dir
     */
    protected $dir;

    /**
     * [protected description]
     * @var Request
     */
    protected $request;

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
     * @param Request $request
     * @param ResponseFactory $response
     * @param Config $config
     * @param Lang $lang
     * @param URL $url
     */
    public function __construct(
        Request $request,
        ResponseFactory $response,
        Config $config,
        Lang $lang,
        URL $url
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->config = $config;
        $this->lang = $lang;
        $this->url = $url;
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
        if ($this->request->input('payment_type') === 'transfer') {
            return $this->response->redirectToRoute('web.payment.dir.show', [
                $this->dir->getPayment()->uuid
            ]);
        }

        switch ($this->dir->status) {
            case Dir::ACTIVE:
                return $this->response->redirectToRoute('web.profile.edit_dir')
                    ->with('success', $this->lang->get('idir::dirs.success.update_renew.'.Dir::ACTIVE));
            default:
                return $this->response->redirectToRoute('web.profile.edit_dir')
                    ->with('success', $this->lang->get('idir::dirs.success.update_renew.'.Dir::INACTIVE));
        }
    }
}
