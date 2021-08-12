<?php

namespace N1ebieski\IDir\Services;

use N1ebieski\IDir\Models\Price;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\DatabaseManager as DB;
use N1ebieski\ICore\Services\Interfaces\Creatable;
use N1ebieski\ICore\Services\Interfaces\Deletable;
use N1ebieski\ICore\Services\Interfaces\Updatable;

class PriceService implements Creatable, Updatable, Deletable
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
            $price = $this->price->make();

            $price->price = $attributes['price'];
            $price->days = $attributes['days'];
            $price->type = $attributes['type'];
            $price->code = $attributes[$attributes['type']]['code'] ?? null;
            $price->token = $attributes[$attributes['type']]['token'] ?? null;
            $price->number = $attributes[$attributes['type']]['number'] ?? null;

            $price->group()->associate($attributes['group']);
            $price->save();

            if (array_key_exists('codes', $attributes[$attributes['type']])) {
                $this->price->codes()->make()
                    ->setRelations(['price' => $price])
                    ->makeService()
                    ->sync($attributes[$attributes['type']]['codes'] ?? []);
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
            $this->price->price = $attributes['price'];
            $this->price->days = $attributes['days'];
            $this->price->type = $attributes['type'];
            $this->price->code = $attributes[$attributes['type']]['code'] ?? null;
            $this->price->token = $attributes[$attributes['type']]['token'] ?? null;
            $this->price->number = $attributes[$attributes['type']]['number'] ?? null;

            $this->price->group()->associate($attributes['group']);

            if (array_key_exists('codes', $attributes[$attributes['type']])) {
                $this->price->codes()->make()
                    ->setRelations(['price' => $this->price])
                    ->makeService()
                    ->sync($attributes[$attributes['type']]['codes'] ?? []);
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
