<?php

namespace N1ebieski\IDir\Services\Field\Value\Types\Interfaces;

interface ArrayInterface
{
    /**
     * Undocumented function
     *
     * @param array $value
     * @return string
     */
    public function prepare(array $value): array;

    /**
     * Undocumented function
     *
     * @param array $value
     * @return array
     */
    public function create(array $value): array;

    /**
     * Undocumented function
     *
     * @param array $value
     * @return array
     */
    public function update(array $value): array;

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function delete(): bool;
}
