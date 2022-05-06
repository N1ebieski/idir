<?php

namespace N1ebieski\IDir\Http\Clients\Payment\Cashbill\Transfer\Responses;

use Psr\Http\Message\ResponseInterface;
use N1ebieski\IDir\Http\Clients\Payment\Interfaces\Transfer\Responses\PurchaseResponseInterface;

class PurchaseResponse implements PurchaseResponseInterface
{
    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * Constructor.
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
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
        $redirects = $this->response->getHeader(\GuzzleHttp\RedirectMiddleware::HISTORY_HEADER);

        return end($redirects);
    }
}
