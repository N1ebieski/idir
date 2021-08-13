<?php

namespace N1ebieski\IDir\Utils\Payment\PayPal;

use Illuminate\Contracts\Config\Repository as Config;
use Mdb\PayPal\Ipn\ListenerBuilder\Guzzle\ArrayListenerBuilder;

class PayPalListener extends ArrayListenerBuilder
{
    /**
     * Undocumented function
     *
     * @param Config $config
     */
    private $config;

    /**
     * @var bool
     */
    private $useSandbox = false;

    /**
     * Undocumented function
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
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
            ($this->config->get('services.paypal.paypal_express.sandbox_check_url') ?? 'https://www.sandbox.paypal.com/cgi-bin/webscr')
            : ($this->config->get('services.paypal.paypal_express.check_url') ?? 'https://www.paypal.com/cgi-bin/webscr');
    }
}
