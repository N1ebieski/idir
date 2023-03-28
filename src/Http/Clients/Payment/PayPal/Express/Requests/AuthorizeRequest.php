<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\IDir\Http\Clients\Payment\PayPal\Express\Requests;

use N1ebieski\ICore\Http\Clients\Request;
use Illuminate\Http\Response as HttpResponse;
use Mdb\PayPal\Ipn\Event\MessageInvalidEvent;
use Illuminate\Contracts\Config\Repository as Config;
use Mdb\PayPal\Ipn\Event\MessageVerificationFailureEvent;
use N1ebieski\IDir\Http\Clients\Payment\PayPal\PayPalListener;

class AuthorizeRequest extends Request
{
    /**
     *
     * @param array $parameters
     * @param Config $config
     * @param PayPalListener $listener
     * @return void
     */
    public function __construct(
        protected array $parameters,
        protected PayPalListener $listener,
        Config $config
    ) {
        $this->setParameters($parameters);

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
    protected function setAmount(string $amount): self
    {
        $this->parameters['mc_gross'] = number_format((float)$amount, 2, '.', '');

        unset($this->parameters['amount']);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $uuid
     * @return self
     */
    protected function setUuid(string $uuid): self
    {
        unset($this->parameters['uuid']);

        return $this;
    }

    /**
     *
     * @return void
     */
    public function makeRequest(): void
    {
        /** @var array */
        $parameters = $this->getParameters();

        $this->listener->setData($parameters);

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
