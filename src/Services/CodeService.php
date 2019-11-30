<?php

namespace N1ebieski\IDir\Services;

use N1ebieski\ICore\Services\Serviceable;
use Illuminate\Database\Eloquent\Model;
use N1ebieski\IDir\Models\Code;
use Carbon\Carbon;

/**
 * [CodeService description]
 */
class CodeService implements Serviceable
{
    /**
     * [private description]
     * @var Code
     */
    protected $code;

    /**
     * @param Code $code
     */
    public function __construct(Code $code)
    {
        $this->code = $code;
    }

    /**
     * [organizeGlobal description]
     * @param array $attributes [description]
     */
    public function organizeGlobal(array $attributes) : void
    {
        if (isset($attributes['sync'])) {
            $this->clear();

            if (isset($attributes['codes'])) {
                $this->createGlobal($attributes['codes']);
            }
        }
    }

    /**
     * [createGlobal description]
     * @param array $attributes [description]
     */
    public function createGlobal(array $attributes) : void
    {
        foreach ($attributes as $attribute) {
            $code = $this->code->make($code);
            $code->price()->associate($this->code->getPrice());
            $code->created_at = Carbon::now();
            $code->updated_at = Carbon::now();

            $codes_model[] = $code->attributesToArray();
        }

        $this->code->insert($codes_model);
    }

    /**
     * [create description]
     * @param  array $attributes [description]
     * @return Model             [description]
     */
    public function create(array $attributes) : Model
    {
        //
    }

    /**
     * [update description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function update(array $attributes) : bool
    {
        //
    }

    /**
     * [updateStatus description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updateStatus(array $attributes) : bool
    {
        //
    }

    /**
     * [delete description]
     * @return bool [description]
     */
    public function delete() : bool
    {
        //
    }

    /**
     * [clear description]
     * @return int [description]
     */
    public function clear() : int
    {
        return $this->code->where('price_id', $this->code->getPrice()->id)->delete();
    }

    /**
     * [deleteGlobal description]
     * @param  array $ids [description]
     * @return int        [description]
     */
    public function deleteGlobal(array $ids) : int
    {
        //
    }
}
