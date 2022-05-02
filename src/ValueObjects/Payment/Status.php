<?php

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
     * @return void
     */
    public static function fromString(string $value)
    {
        if (
            in_array($value, [
            'pending',
            Type::TRANSFER,
            Type::PAYPAL_EXPRESS,
            (string)self::PENDING
            ])
        ) {
            return static::pending();
        }

        if (in_array($value, ['finished', (string)self::FINISHED])) {
            return static::finished();
        }

        if (in_array($value, ['unfinished', (string)self::UNFINISHED])) {
            return static::unfinished();
        }

        throw new \InvalidArgumentException("Invalid string value: '{$value}'");
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public static function pending()
    {
        return new static(self::PENDING);
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public static function finished()
    {
        return new static(self::FINISHED);
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public static function unfinished()
    {
        return new static(self::UNFINISHED);
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
