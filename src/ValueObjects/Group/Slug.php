<?php

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
     * @return static
     */
    public static function default()
    {
        return new static(self::DEFAULT);
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
