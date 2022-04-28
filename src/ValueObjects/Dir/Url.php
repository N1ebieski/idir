<?php

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
     * Undocumented function
     *
     * @return string
     */
    public function getHost(): string
    {
        return $this->isUrl() ? parse_url($this->value, PHP_URL_HOST) : null;
    }
}
