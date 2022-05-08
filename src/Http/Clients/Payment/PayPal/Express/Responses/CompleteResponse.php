<?php

namespace N1ebieski\IDir\Http\Clients\Payment\PayPal\Express\Responses;

use Omnipay\Common\Message\ResponseInterface as OmniPayResponse;
use N1ebieski\IDir\Http\Clients\Payment\Interfaces\Transfer\Responses\CompleteResponseInterface;

/**
 *
 * @author Mariusz Wysokiński <kontakt@intelekt.net.pl>
 */
class CompleteResponse implements CompleteResponseInterface
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
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->response->isSuccessful();
    }
}
