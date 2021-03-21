<?php

namespace N1ebieski\IDir\Services;

use N1ebieski\IDir\Models\Code;
use Illuminate\Support\Carbon;

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
     * Undocumented function
     *
     * @param array $attributes
     * @return boolean
     */
    protected function isSync(array $attributes) : bool
    {
        return isset($attributes['sync']) || empty($attributes);
    }

    /**
     * Undocumented function
     *
     * @param array $attributes
     * @return void
     */
    public function sync(array $attributes) : void
    {
        if (!$this->isSync($attributes)) {
            return;
        }

        $this->clear();

        if (isset($attributes['codes'])) {
            $this->createGlobal($attributes['codes']);
        }
    }

    /**
     * [createGlobal description]
     * @param array $attributes [description]
     */
    public function createGlobal(array $attributes) : void
    {
        foreach ($attributes as $attribute) {
            // Create attributes manually, no within model because multiple
            // models may be huge performance impact
            $codes[] = [
                'price_id' => $this->code->price->id,
                'code' => $attribute['code'],
                'quantity' => $attribute['quantity'],
                'created_at' => $this->carbon->now(),
                'updated_at' => $this->carbon->now()
            ];
        }

        $this->code->insert($codes);
    }

    /**
     * [clear description]
     * @return int [description]
     */
    public function clear() : int
    {
        return $this->code->where('price_id', $this->code->price->id)->delete();
    }
}
