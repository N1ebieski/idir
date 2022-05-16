<?php

namespace N1ebieski\IDir\Services\Price;

use N1ebieski\IDir\Models\Price;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\DatabaseManager as DB;
use N1ebieski\ICore\Services\Interfaces\CreateInterface;
use N1ebieski\ICore\Services\Interfaces\DeleteInterface;
use N1ebieski\ICore\Services\Interfaces\UpdateInterface;

class PriceService implements CreateInterface, UpdateInterface, DeleteInterface
{
    /**
     * Model
     * @var Price
     */
    protected $price;

    /**
     * Undocumented variable
     *
     * @var DB
     */
    protected $db;

    /**
     * Undocumented function
     *
     * @param Price $price
     * @param DB $db
     */
    public function __construct(Price $price, DB $db)
    {
        $this->price = $price;

        $this->db = $db;
    }

    /**
     * [create description]
     * @param  array $attributes [description]
     * @return Model             [description]
     */
    public function create(array $attributes): Model
    {
        return $this->db->transaction(function () use ($attributes) {
            $price = $this->price->make($attributes);

            $price->group()->associate($attributes['group']);

            $price->save();

            if (array_key_exists('codes', $attributes)) {
                $this->price->codes()->make()
                    ->setRelations(['price' => $price])
                    ->makeService()
                    ->sync($attributes['codes'] ?? []);
            }

            return $price;
        });
    }

    /**
     * [update description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function update(array $attributes): bool
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->price->fill($attributes);

            if (array_key_exists('codes', $attributes)) {
                $this->price->codes()->make()
                    ->setRelations(['price' => $this->price])
                    ->makeService()
                    ->sync($attributes['codes'] ?? []);
            }

            if (array_key_exists('group', $attributes)) {
                $this->price->group()->associate($attributes['group']);
            }

            return $this->price->save();
        });
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function delete(): bool
    {
        return $this->db->transaction(function () {
            return $this->price->delete();
        });
    }
}
