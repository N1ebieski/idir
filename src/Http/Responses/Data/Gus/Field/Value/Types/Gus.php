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

namespace N1ebieski\IDir\Http\Responses\Data\Gus\Field\Value\Types;

use GusApi\SearchReport as GusReport;
use N1ebieski\IDir\Http\Responses\Data\Gus\Field\Value\Types\Value;

class Gus extends Value
{
    /**
     * Undocumented function
     *
     * @param string $type
     * @param GusReport $gusReport
     */
    public function __construct(
        protected string $type,
        GusReport $gusReport
    ) {
        parent::__construct($gusReport);
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function isMethodExists(): bool
    {
        return method_exists($this->gusReport, $this->methodName());
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function methodName(): string
    {
        return 'get' . ucfirst($this->type);
    }

    /**
     * Undocumented function
     *
     * @return string|null
     */
    public function handle(): ?string
    {
        if ($this->isMethodExists()) {
            $method = $this->methodName();

            return $this->gusReport->$method();
        }

        return null;
    }
}
