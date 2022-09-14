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

namespace N1ebieski\IDir\Http\Responses\Data\Gus\Field\Value;

use GusApi\SearchReport as GusReport;
use Illuminate\Contracts\Container\Container as App;
use N1ebieski\IDir\Http\Responses\Data\Gus\Field\Value\Types\Gus;
use N1ebieski\IDir\Http\Responses\Data\Gus\Field\Value\Types\Value;

class ValueFactory
{
    /**
     * Undocumented function
     *
     * @param App $app
     */
    public function __construct(protected App $app)
    {
        $this->app = $app;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function isClassExists(string $type): bool
    {
        return class_exists($this->className($type)) || $this->app->bound($this->className($type));
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function className(string $type): string
    {
        return "N1ebieski\\IDir\\Http\\Responses\\Data\\Gus\\Field\\Value\\Types\\" . ucfirst($type);
    }

    /**
     * Undocumented function
     *
     * @param string $type
     * @param GusReport $gusReport
     * @return Value
     */
    public function makeValue(string $type, GusReport $gusReport): Value
    {
        if ($this->isClassExists($type)) {
            return $this->app->make($this->className($type), ['gusReport' => $gusReport]);
        }

        return $this->app->make(Gus::class, [
            'type' => $type,
            'gusReport' => $gusReport
        ]);
    }
}
