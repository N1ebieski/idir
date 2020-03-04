<?php

namespace N1ebieski\IDir\Http\Responses\Web\Dir;

use Illuminate\Http\Request;
use N1ebieski\IDir\Models\Dir;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Translation\Translator as Lang;
use Illuminate\Http\RedirectResponse;

/**
 * [UpdateRenewResponse description]
 */
class UpdateRenewResponse
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
     * Undocumented function
     *
     * @param Request $request
     * @param ResponseFactory $response
     * @param Config $config
     * @param Lang $lang
     */
    public function __construct(Request $request, ResponseFactory $response, Config $config, Lang $lang)
    {
        $this->request = $request;
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
        if ($this->request->input('payment_type') === 'transfer') {
            return $this->response->redirectToRoute('web.payment.dir.show', [$this->dir->getPayment()->uuid]);
        }

        switch ($this->dir->status) {
            case Dir::ACTIVE:
                return $this->response->redirectToRoute('web.profile.edit_dir')
                    ->with('success', $this->lang->get('idir::dirs.success.update_renew.status_1'));
            default:
                return $this->response->redirectToRoute('web.profile.edit_dir')
                    ->with('success', $this->lang->get('idir::dirs.success.update_renew.status_0'));
        }
    }
}
