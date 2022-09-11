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

namespace N1ebieski\IDir\ValueObjects\Group;

use N1ebieski\ICore\ValueObjects\ValueObject;

class Slug extends ValueObject
{
    /**
     * [public description]
     * @var string
     */
    public const DEFAULT = 'default';

    /**
     * Undocumented function
     *
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * Undocumented function
     *
     * @return self
     */
    public static function default(): self
    {
        return new self(self::DEFAULT);
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isDefault(): bool
    {
        return $this->value === self::DEFAULT;
    }
}
