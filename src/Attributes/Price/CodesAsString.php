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

namespace N1ebieski\IDir\Attributes\Price;

use N1ebieski\IDir\Models\Price as Price;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Casts\Attribute;

class CodesAsString
{
    /**
     *
     * @param Price $price
     * @return void
     */
    public function __construct(protected Price $price)
    {
        //
    }

    /**
     *
     * @return Attribute
     */
    public function __invoke(): Attribute
    {
        return new Attribute(
            get: function (): ?string {
                if ($this->price->codes instanceof Collection && $this->price->codes->isNotEmpty()) {
                    $codes = [];

                    foreach ($this->price->codes as $code) {
                        $codes[] = $code->code . '|' . ($code->quantity !== null ? $code->quantity : 0);
                    }

                    return (string)implode("\r\n", $codes);
                }

                return null;
            }
        );
    }
}
