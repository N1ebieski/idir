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

namespace N1ebieski\IDir\ValueObjects\Payment;

use N1ebieski\IDir\ValueObjects\Price\Type;
use N1ebieski\ICore\ValueObjects\ValueObject;

class Status extends ValueObject
{
    /**
     * [public description]
     * @var int
     */
    public const FINISHED = 1;

    /**
     * [public description]
     * @var int
     */
    public const UNFINISHED = 0;

    /**
     * [public description]
     * @var int
     */
    public const PENDING = 2;

    /**
     * Undocumented function
     *
     * @param integer $value
     */
    public function __construct(int $value)
    {
        $this->validate($value);

        $this->value = $value;
    }

    /**
     * Undocumented function
     *
     * @param integer $value
     * @return void
     */
    protected function validate(int $value): void
    {
        $in = [
            self::PENDING,
            self::FINISHED,
            self::UNFINISHED
        ];

        if (!in_array($value, $in)) {
            throw new \InvalidArgumentException("The given value must be in: " . implode(', ', $in));
        }
    }

    /**
     * Undocumented function
     *
     * @param string $value
     * @return self
     */
    public static function fromString(string $value): self
    {
        if (
            in_array($value, [
                'pending',
                Type::TRANSFER,
                Type::PAYPAL_EXPRESS,
                (string)self::PENDING
            ])
        ) {
            return self::pending();
        }

        if (in_array($value, ['finished', (string)self::FINISHED])) {
            return self::finished();
        }

        if (in_array($value, ['unfinished', (string)self::UNFINISHED])) {
            return self::unfinished();
        }

        throw new \InvalidArgumentException("Invalid string value: '{$value}'");
    }

    /**
     * Undocumented function
     *
     * @return self
     */
    public static function pending(): self
    {
        return new self(self::PENDING);
    }

    /**
     * Undocumented function
     *
     * @return self
     */
    public static function finished(): self
    {
        return new self(self::FINISHED);
    }

    /**
     * Undocumented function
     *
     * @return self
     */
    public static function unfinished(): self
    {
        return new self(self::UNFINISHED);
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isPending(): bool
    {
        return $this->value === self::PENDING;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isFinished(): bool
    {
        return $this->value === self::FINISHED;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isUnfinished(): bool
    {
        return $this->value === self::UNFINISHED;
    }
}
