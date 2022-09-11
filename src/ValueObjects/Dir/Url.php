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

namespace N1ebieski\IDir\ValueObjects\Dir;

use N1ebieski\ICore\ValueObjects\ValueObject;

class Url extends ValueObject
{
    /**
     * Undocumented function
     *
     * @param string $value
     */
    public function __construct(string $value = null)
    {
        if (is_string($value)) {
            $this->validate($value);
        }

        $this->value = $value;
    }

    /**
     * Undocumented function
     *
     * @param string $value
     * @return void
     */
    protected function validate(string $value): void
    {
        if (!preg_match('/^(https|http):\/\/([\da-z\.-]+)(\.[a-z]{2,6})(\/?$|\/.*)/', $value)) {
            throw new \InvalidArgumentException("The given value must be valid url structure.");
        }
    }

    /**
     * [isUrl description]
     * @return bool [description]
     */
    public function isUrl(): bool
    {
        return $this->value !== null;
    }

    /**
     *
     * @return null|string
     */
    public function getHost(): ?string
    {
        return $this->isUrl() ?
            (parse_url($this->value, PHP_URL_HOST) ?: '')
            : null;
    }
}
