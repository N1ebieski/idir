<?php

namespace N1ebieski\IDir\ValueObjects\Dir;

use InvalidArgumentException;
use N1ebieski\ICore\ValueObjects\ValueObject;

class Comment extends ValueObject
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
     *
     * @param int $value
     * @return void
     * @throws InvalidArgumentException
     */
    protected function validate(int $value): void
    {
        $in = [self::ACTIVE, self::INACTIVE];

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
}
