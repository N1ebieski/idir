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

namespace N1ebieski\IDir\ValueObjects\Field;

use N1ebieski\ICore\ValueObjects\ValueObject;
use N1ebieski\IDir\ValueObjects\Field\Required;

/**
 * @property Required $required
 * @property array $options
 * @property int $min
 * @property int $max
 * @property int $height
 * @property int $width
 * @property int $size
 */
class Options extends ValueObject
{
    /**
     * Undocumented function
     *
     * @param object $value
     */
    public function __construct(object $value)
    {
        $this->validate($value);

        $this->value = (object)[];

        // @phpstan-ignore-next-line
        $this->setRequired($value->required);

        if (property_exists($value, 'options')) {
            $this->setOptions($value->options);
        }

        if (property_exists($value, 'min')) {
            $this->setMin($value->min);
        }

        if (property_exists($value, 'max')) {
            $this->setMax($value->max);
        }

        if (property_exists($value, 'height')) {
            $this->setHeight($value->height);
        }

        if (property_exists($value, 'width')) {
            $this->setWidth($value->width);
        }

        if (property_exists($value, 'size')) {
            $this->setSize($value->size);
        }
    }

    /**
     * Undocumented function
     *
     * @param mixed $value
     * @return self
     */
    public function setRequired($value)
    {
        if (is_string($value)) {
            $this->value->required = Required::fromString($value);
        }

        if (is_int($value)) {
            $this->value->required = new Required($value);
        }

        if (!$this->value->required instanceof Required) {
            throw new \InvalidArgumentException('The given value is not a Required instance');
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param integer $value
     * @return self
     */
    public function setMin(int $value): self
    {
        $this->value->min = $value;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param integer $value
     * @return self
     */
    public function setMax(int $value): self
    {
        $this->value->max = $value;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param integer $value
     * @return self
     */
    public function setHeight(int $value): self
    {
        $this->value->height = $value;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param integer $value
     * @return self
     */
    public function setWidth(int $value): self
    {
        $this->value->width = $value;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param integer $value
     * @return self
     */
    public function setSize(int $value): self
    {
        $this->value->size = $value;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array $value
     * @return self
     */
    public function setOptions(array $value): self
    {
        $this->value->options = $value;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string|null
     */
    public function getOptionsAsString(): ?string
    {
        if (property_exists($this->value, 'options')) {
            return implode("\r\n", $this->value->options);
        }

        return null;
    }

    /**
     * Undocumented function
     *
     * @param object $value
     * @return void
     */
    protected function validate(object $value): void
    {
        if (!property_exists($value, 'required')) {
            throw new \InvalidArgumentException("The given value must have 'required' property");
        }
    }

    /**
     * Undocumented function
     *
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        if (property_exists($this->value, $name)) {
            return $this->value->{$name};
        }

        return null;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function __toString()
    {
        return json_encode($this->value) ?: '';
    }
}
