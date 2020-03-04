<?php

namespace N1ebieski\IDir\Services;

use N1ebieski\IDir\Models\Code;
use Illuminate\Support\Carbon;

/**
 * [CodeService description]
 */
class CodeService
{
    /**
     * [private description]
     * @var Code
     */
    protected $code;

    /**
     * Undocumented variable
     *
     * @var Carbon
     */
    protected $carbon;

    /**
     * Undocumented function
     *
     * @param Code $code
     * @param Carbon $carbon
     */
    public function __construct(Code $code, Carbon $carbon)
    {
        $this->code = $code;

        $this->carbon = $carbon;
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
            $code = $this->code->make($attribute);
            $code->price()->associate($this->code->getPrice());
            $code->created_at = $this->carbon->now();
            $code->updated_at = $this->carbon->now();

            $codes_model[] = $code->attributesToArray();
        }

        $this->code->insert($codes_model);
    }

    /**
     * [clear description]
     * @return int [description]
     */
    public function clear() : int
    {
        return $this->code->where('price_id', $this->code->getPrice()->id)->delete();
    }
}
