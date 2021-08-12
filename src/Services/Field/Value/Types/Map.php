<?php

namespace N1ebieski\IDir\Services\Field\Value\Types;

use N1ebieski\IDir\Services\Field\Value\Types\Value;

class Map extends Value
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
        return $this->updateOrCreate($value);
    }

    /**
     * Undocumented function
     *
     * @param array $value
     * @return array
     */
    public function update(array $value): array
    {
        return $this->updateOrCreate($value);
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function delete(): bool
    {
        return $this->db->transaction(function () {
            return $this->field->morph->map()->delete();
        });
    }

    /**
     * Undocumented function
     *
     * @param array $value
     * @return array
     */
    protected function updateOrCreate(array $value): array
    {
        return $this->db->transaction(function () use ($value) {
            if (count($value) > 0) {
                $this->field->morph->map()->updateOrCreate([], [
                    'lat' => $value[0]['lat'],
                    'long' => $value[0]['long']
                ]);
            }

            return $value;
        });
    }
}
