<?php

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
     * @return void
     */
    public static function fromString(string $value)
    {
        if (in_array($value, ['active', self::ACTIVE])) {
            return static::active();
        }

        if (in_array($value, ['inactive', self::INACTIVE])) {
            return static::inactive();
        }

        if (
            in_array($value, [
            'payment_inactive',
            'pending',
            Type::TRANSFER,
            Type::PAYPAL_EXPRESS,
            self::PAYMENT_INACTIVE
            ])
        ) {
            return static::paymentInactive();
        }

        if (in_array($value, ['backlink_inactive', self::BACKLINK_INACTIVE])) {
            return static::backlinkInactive();
        }

        if (in_array($value, ['status_inactive', self::STATUS_INACTIVE])) {
            return static::statusInactive();
        }

        if (in_array($value, ['incorrect_inactive', self::INCORRECT_INACTIVE])) {
            return static::incorrectInactive();
        }

        throw new \InvalidArgumentException("Invalid string value: '{$value}'");
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public static function active()
    {
        return new static(self::ACTIVE);
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public static function inactive()
    {
        return new static(self::INACTIVE);
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public static function paymentInactive()
    {
        return new static(self::PAYMENT_INACTIVE);
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public static function backlinkInactive()
    {
        return new static(self::BACKLINK_INACTIVE);
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public static function statusInactive()
    {
        return new static(self::STATUS_INACTIVE);
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public static function incorrectInactive()
    {
        return new static(self::INCORRECT_INACTIVE);
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
