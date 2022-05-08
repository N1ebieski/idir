<?php

namespace N1ebieski\IDir\Http\Clients\Payment\PayPal\Express\Requests;

use N1ebieski\ICore\Http\Clients\Request;
use Illuminate\Http\Response as HttpResponse;
use Mdb\PayPal\Ipn\Event\MessageInvalidEvent;
use Illuminate\Contracts\Config\Repository as Config;
use Mdb\PayPal\Ipn\Event\MessageVerificationFailureEvent;
use N1ebieski\IDir\Http\Clients\Payment\PayPal\PayPalListener;

/**
 *
 * @author Mariusz Wysokiński <kontakt@intelekt.net.pl>
 */
class AuthorizeRequest extends Request
{
    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $parameters;

    /**
     * Undocumented variable
     *
     * @var PayPalListener
     */
    protected $listener;

    /**
     *
     * @param array $parameters
     * @param Config $config
     * @param PayPalListener $listener
     * @return void
     */
    public function __construct(array $parameters, Config $config, PayPalListener $listener)
    {
        $this->parameters = $parameters;

        $this->setParameters($parameters);

        $this->listener = $listener;

        if ((bool)$config->get('services.paypal.paypal_express.sandbox') === true) {
            $this->listener->useSandbox();
        }
    }

    /**
     * Undocumented function
     *
     * @param string $amount
     * @return self
     */
    protected function setAmount(string $amount)
    {
        $this->parameters['mc_gross'] = number_format($amount, 2, '.', '');

        unset($this->parameters['amount']);

        return $this;
    }

    /**
     *
     * @return void
     */
    public function makeRequest(): void
    {
        $this->listener->setData($this->getParameters());

        $listener = $this->listener->build();

        $listener->onInvalid(function (MessageInvalidEvent $event) {
            throw new \N1ebieski\IDir\Exceptions\Payment\PayPal\Express\InvalidException(
                $event->getMessage(),
                HttpResponse::HTTP_FORBIDDEN
            );
        });

        $listener->onVerificationFailure(function (MessageVerificationFailureEvent $event) {
            throw new \N1ebieski\IDir\Exceptions\Payment\PayPal\VerificationFailureException(
                $event->getError(),
                HttpResponse::HTTP_FORBIDDEN
            );
        });

        $listener->listen();
    }
}
