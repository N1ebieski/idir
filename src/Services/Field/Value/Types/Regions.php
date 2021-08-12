<?php

namespace N1ebieski\IDir\Services\Field\Value\Types;

use N1ebieski\IDir\Services\Field\Value\Types\Value;

class Regions extends Value
{
    /**
     * Undocumented function
     *
     * @param array $value
     * @return string
     */
    public function prepare(array $value): array
    {
        return $value;
    }

    /**
     * Undocumented function
     *
     * @param array $value
     * @return array
     */
    public function create(array $value): array
    {
        return $this->db->transaction(function () use ($value) {
            $this->field->morph->regions()->attach($value ?? []);

            return $value;
        });
    }

    /**
     * Undocumented function
     *
     * @param array $value
     * @return array
     */
    public function update(array $value): array
    {
        return $this->db->transaction(function () use ($value) {
            $this->field->morph->regions()->sync($value ?? []);

            return $value;
        });
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function delete(): bool
    {
        return false;
    }    
}
