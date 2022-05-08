<?php

namespace N1ebieski\IDir\Http\Responses\Web\Dir;

use Illuminate\Http\Request;
use N1ebieski\IDir\Models\Dir;
use Illuminate\Http\RedirectResponse;
use N1ebieski\IDir\ValueObjects\Dir\Status;
use N1ebieski\IDir\ValueObjects\Price\Type;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Routing\UrlGenerator as URL;
use Illuminate\Contracts\Translation\Translator as Lang;
use N1ebieski\IDir\Http\Responses\Web\Dir\RedirectResponseFactory;

class UpdateRenewResponse implements RedirectResponseFactory
{
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
     * Undocumented function
     *
     * @param Dir $dir
     * @return RedirectResponse
     */
    public function makeResponse(Dir $dir): RedirectResponse
    {
        if (in_array($this->request->input('payment_type'), [Type::TRANSFER, Type::PAYPAL_EXPRESS])) {
            return $this->response->redirectToRoute('web.payment.dir.show', [
                $dir->payment->uuid,
                $dir->payment->driver
            ]);
        }

        switch ($dir->status->getValue()) {
            case Status::ACTIVE:
                return $this->response->redirectToRoute('web.profile.dirs')
                    ->with('success', $this->lang->get('idir::dirs.success.update_renew.' . Status::ACTIVE));
            default:
                return $this->response->redirectToRoute('web.profile.dirs')
                    ->with('success', $this->lang->get('idir::dirs.success.update_renew.' . Status::INACTIVE));
        }
    }
}
