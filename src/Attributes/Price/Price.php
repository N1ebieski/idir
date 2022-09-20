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

use N1ebieski\IDir\Models\Price as PriceModel;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Price
{
    /**
     *
     * @param PriceModel $price
     * @return void
     */
    public function __construct(protected PriceModel $price)
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
            get: function (): string {
                return is_null($this->price->discount_price) ?
                    $this->price->regular_price
                    : $this->price->discount_price;
            }
        );
    }
}
