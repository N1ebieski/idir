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

namespace N1ebieski\IDir\Services\Price;

use Throwable;
use N1ebieski\IDir\Models\Code;
use N1ebieski\IDir\Models\Price;
use Illuminate\Database\DatabaseManager as DB;

class PriceService
{
    /**
     * Undocumented function
     *
     * @param Price $price
     * @param DB $db
     */
    public function __construct(
        protected Price $price,
        protected DB $db
    ) {
        //
    }

    /**
     *
     * @param array $attributes
     * @return Price
     * @throws Throwable
     */
    public function create(array $attributes): Price
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->price->fill($attributes);

            $this->price->group()->associate($attributes['group']);

            $this->price->save();

            if (array_key_exists('codes', $attributes)) {
                /** @var Code */
                $code = $this->price->codes()->make();

                $code->makeService()->sync(array_merge([
                    'price' => $this->price
                ], $attributes['codes']));
            }

            return $this->price;
        });
    }

    /**
     *
     * @param array $attributes
     * @return Price
     * @throws Throwable
     */
    public function update(array $attributes): Price
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->price->fill($attributes);

            if (array_key_exists('group', $attributes)) {
                $this->price->group()->associate($attributes['group']);
            }

            $this->price->save();

            if (array_key_exists('codes', $attributes)) {
                /** @var Code */
                $code = $this->price->codes()->make();

                $code->makeService()->sync(array_merge([
                    'price' => $this->price
                ], $attributes['codes']));
            }

            return $this->price;
        });
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function delete(): bool
    {
        return $this->db->transaction(function () {
            return $this->price->delete();
        });
    }
}
