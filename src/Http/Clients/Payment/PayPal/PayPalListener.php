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

namespace N1ebieski\IDir\Http\Clients\Payment\PayPal;

use Illuminate\Contracts\Config\Repository as Config;
use Mdb\PayPal\Ipn\ListenerBuilder\Guzzle\ArrayListenerBuilder;

class PayPalListener extends ArrayListenerBuilder
{
    /**
     * @var bool
     */
    private $useSandbox = false;

    /**
     * Undocumented function
     *
     * @param Config $config
     */
    public function __construct(protected Config $config)
    {
        //
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function useSandbox()
    {
        $this->useSandbox = true;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function getServiceEndpoint(): string
    {
        return ($this->useSandbox) ?
            $this->config->get(
                'services.paypal.paypal_express.sandbox_check_url',
                'https://www.sandbox.paypal.com/cgi-bin/webscr'
            ) : $this->config->get(
                'services.paypal.paypal_express.check_url',
                'https://www.paypal.com/cgi-bin/webscr'
            );
    }
}
