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

namespace N1ebieski\IDir\Services\Field\Value\Types;

use N1ebieski\IDir\Services\Field\Value\Types\Value;
use N1ebieski\IDir\Services\Field\Value\Types\Interfaces\ArrayInterface;

class Regions extends Value implements ArrayInterface
{
    /**
     *
     * @param array $value
     * @return array
     */
    public function prepare(array $value): array
    {
        return $value;
    }

    /**
     * Undocumented function
     *
     * @param array $value
     * @return array
     */
    public function create(array $value): array
    {
        return $this->db->transaction(function () use ($value) {
            $this->field->morph->regions()->attach($value);

            return $value;
        });
    }

    /**
     * Undocumented function
     *
     * @param array $value
     * @return array
     */
    public function update(array $value): array
    {
        return $this->db->transaction(function () use ($value) {
            $this->field->morph->regions()->sync($value);

            return $value;
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
            return $this->field->morph->regions()->detach();
        });
    }
}
