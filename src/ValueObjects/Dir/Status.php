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

use N1ebieski\IDir\ValueObjects\Price\Type;
use N1ebieski\ICore\ValueObjects\ValueObject;

class Status extends ValueObject
{
    /**
     * [public description]
     * @var int
     */
    public const ACTIVE = 1;

    /**
     * [public description]
     * @var int
     */
    public const INACTIVE = 0;

    /**
     * [public description]
     * @var int
     */
    public const PAYMENT_INACTIVE = 2;

    /**
     * [public description]
     * @var int
     */
    public const BACKLINK_INACTIVE = 3;

    /**
     * [public description]
     * @var int
     */
    public const STATUS_INACTIVE = 4;

    /**
     * [public description]
     * @var int
     */
    public const INCORRECT_INACTIVE = 5;

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
            self::ACTIVE,
            self::INACTIVE,
            self::PAYMENT_INACTIVE,
            self::BACKLINK_INACTIVE,
            self::STATUS_INACTIVE,
            self::INCORRECT_INACTIVE
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
        if (in_array($value, ['active', (string)self::ACTIVE])) {
            return self::active();
        }

        if (in_array($value, ['inactive', (string)self::INACTIVE])) {
            return self::inactive();
        }

        if (
            in_array($value, [
                'payment_inactive',
                'pending',
                Type::TRANSFER,
                Type::PAYPAL_EXPRESS,
                (string)self::PAYMENT_INACTIVE
            ])
        ) {
            return self::paymentInactive();
        }

        if (in_array($value, ['backlink_inactive', (string)self::BACKLINK_INACTIVE])) {
            return self::backlinkInactive();
        }

        if (in_array($value, ['status_inactive', (string)self::STATUS_INACTIVE])) {
            return self::statusInactive();
        }

        if (in_array($value, ['incorrect_inactive', (string)self::INCORRECT_INACTIVE])) {
            return self::incorrectInactive();
        }

        throw new \InvalidArgumentException("Invalid string value: '{$value}'");
    }

    /**
     * Undocumented function
     *
     * @return self
     */
    public static function active(): self
    {
        return new self(self::ACTIVE);
    }

    /**
     * Undocumented function
     *
     * @return self
     */
    public static function inactive(): self
    {
        return new self(self::INACTIVE);
    }

    /**
     * Undocumented function
     *
     * @return self
     */
    public static function paymentInactive(): self
    {
        return new self(self::PAYMENT_INACTIVE);
    }

    /**
     * Undocumented function
     *
     * @return self
     */
    public static function backlinkInactive(): self
    {
        return new self(self::BACKLINK_INACTIVE);
    }

    /**
     * Undocumented function
     *
     * @return self
     */
    public static function statusInactive(): self
    {
        return new self(self::STATUS_INACTIVE);
    }

    /**
     * Undocumented function
     *
     * @return self
     */
    public static function incorrectInactive(): self
    {
        return new self(self::INCORRECT_INACTIVE);
    }

    /**
     * [isActive description]
     * @return bool [description]
     */
    public function isActive(): bool
    {
        return $this->value === self::ACTIVE;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isInactive(): bool
    {
        return $this->value === self::INACTIVE;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isPaymentInactive(): bool
    {
        return $this->value === self::PAYMENT_INACTIVE;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isBacklinkInactive(): bool
    {
        return $this->value === self::BACKLINK_INACTIVE;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isStatusInactive(): bool
    {
        return $this->value === self::STATUS_INACTIVE;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isIncorrectInactive(): bool
    {
        return $this->value === self::INCORRECT_INACTIVE;
    }

    /**
     * [isUpdateStatus description]
     * @return bool [description]
     */
    public function isUpdateStatus(): bool
    {
        return in_array($this->value, [
            self::ACTIVE,
            self::INACTIVE,
            self::INCORRECT_INACTIVE
        ]);
    }
}
