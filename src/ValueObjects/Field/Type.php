<?php

namespace N1ebieski\IDir\ValueObjects\Field;

use N1ebieski\ICore\ValueObjects\ValueObject;

class Type extends ValueObject
{
    /**
     * [public description]
     * @var string
     */
    public const INPUT = 'input';

    /**
     * [public description]
     * @var string
     */
    public const TEXTAREA = 'textarea';

    /**
     * [public description]
     * @var string
     */
    public const SELECT = 'select';

    /**
     * [public description]
     * @var string
     */
    public const MULTISELECT = 'multiselect';

    /**
     * [public description]
     * @var string
     */
    public const CHECKBOX = 'checkbox';

    /**
     * [public description]
     * @var string
     */
    public const IMAGE = 'image';

    /**
     * [public description]
     * @var string
     */
    public const REGIONS = 'regions';

    /**
     * [public description]
     * @var string
     */
    public const MAP = 'map';

    /**
     * [public description]
     * @var string
     */
    public const GUS = 'gus';

    /**
     * Undocumented function
     *
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->validate($value);

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
        $in = [
            self::INPUT,
            self::TEXTAREA,
            self::SELECT,
            self::MULTISELECT,
            self::CHECKBOX,
            self::IMAGE,
            self::REGIONS,
            self::MAP,
            self::GUS
        ];

        if (!in_array($value, $in)) {
            throw new \InvalidArgumentException("The given value must be in: " . implode(', ', $in));
        }
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public static function getAvailable(): array
    {
        return [
            self::INPUT,
            self::TEXTAREA,
            self::SELECT,
            self::MULTISELECT,
            self::CHECKBOX,
            self::IMAGE
        ];
    }

    /**
    * Undocumented function
    *
    * @return array
    */
    public static function getDefault(): array
    {
        return [
            self::REGIONS,
            self::MAP,
            self::GUS
        ];
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isInput(): bool
    {
        return $this->value === self::INPUT;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isTextarea(): bool
    {
        return $this->value === self::TEXTAREA;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isSelect(): bool
    {
        return $this->value === self::SELECT;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isMultiselect(): bool
    {
        return $this->value === self::MULTISELECT;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isCheckbox(): bool
    {
        return $this->value === self::CHECKBOX;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isImage(): bool
    {
        return $this->value === self::IMAGE;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isMap(): bool
    {
        return $this->value === self::MAP;
    }

    /**
     * [isNotDefault description]
     * @return bool [description]
     */
    public function isDefault(): bool
    {
        return in_array($this->value, self::getDefault());
    }
}