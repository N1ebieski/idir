<?php

namespace N1ebieski\IDir\Http\Responses\Web\Dir;

use Illuminate\Http\Request;
use N1ebieski\IDir\Models\Dir;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Config\Repository as Config;
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
     * [__construct description]
     * @param Request         $request  [description]
     * @param ResponseFactory $response [description]
     * @param Config          $config   [description]
     */
    public function __construct(Request $request, ResponseFactory $response, Config $config)
    {
        $this->request = $request;
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
        if ($this->request->input('payment_type') === 'transfer') {
            return $this->response->redirectToRoute('web.payment.dir.show', [$this->dir->getPayment()->uuid]);
        }

        switch ($this->dir->status) {
            case 1:
                return $this->response->redirectToRoute('web.profile.edit_dir')
                    ->with('success', trans('idir::dirs.success.update_renew.status_1'));
            default:
                return $this->response->redirectToRoute('web.profile.edit_dir')
                    ->with('success', trans('idir::dirs.success.update_renew.status_0'));
        }
    }
}
