<?php

namespace N1ebieski\IDir\View\Composers;

use N1ebieski\ICore\View\Composers\Composer;
use Illuminate\Contracts\Config\Repository as Config;

class DriverComposer extends Composer
{
    /**
     * Undocumented variable
     *
     * @var Config
     */
    protected $config;

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
     * @param string $type
     * @return string
     */
    public function driverByType(string $type): string
    {
        return $this->config->get("idir.payment.{$type}.driver");
    }
}
