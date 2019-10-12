<?php

namespace N1ebieski\IDir\Responses\Web\Dir;

use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Payment\Payment;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Http\RedirectResponse;

/**
 * [StoreSummaryRedirect description]
 */
class StoreSummaryResponse
{
    /**
     * [private description]
     * @var Dir
     */
    private $dir;

    /**
     * [private description]
     * @var Payment
     */
    private $payment;

    /**
     * [private description]
     * @var ResponseFactory
     */
    private $response;

    /**
     * [private description]
     * @var Config
     */
    private $config;

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
     * @param Payment $payment
     *
     * @return static
     */
    public function setPayment(Payment $payment)
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * [response description]
     * @return RedirectResponse [description]
     */
    public function response() : RedirectResponse
    {
        switch ($this->dir->status) {
            case 0:
                return $this->response->redirectToRoute('web.dir.create_group')
                    ->with('success', trans('idir::dirs.success.store.status_0'));
            case 1:
                return $this->response->redirectToRoute('web.dir.show', [$this->dir->slug])
                    ->with('success', trans('idir::dirs.success.store.status_1'));
            case 2:
                return $this->response->redirectToRoute('web.payment.show', [$this->payment->id]);
        }
    }
}
