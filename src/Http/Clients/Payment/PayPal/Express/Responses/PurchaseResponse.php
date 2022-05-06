<?php

namespace N1ebieski\IDir\Http\Clients\Payment\PayPal\Express\Responses;

use Omnipay\Common\Message\RedirectResponseInterface as OmniPayResponse;
use N1ebieski\IDir\Http\Clients\Payment\Interfaces\Transfer\Responses\PurchaseResponseInterface;

class PurchaseResponse implements PurchaseResponseInterface
{
    /**
     * @var OmniPayResponse
     */
    protected $response;

    /**
     * Constructor.
     * @param OmniPayResponse $response
     */
    public function __construct(OmniPayResponse $response)
    {
        $this->response = $response;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getUrlToPayment(): string
    {
        return $this->response->getRedirectUrl();
    }
}
