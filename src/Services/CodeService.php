<?php

namespace N1ebieski\IDir\Services;

use Illuminate\Support\Carbon;
use N1ebieski\IDir\Models\Code;
use Illuminate\Database\DatabaseManager as DB;

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
     * Undocumented variable
     *
     * @var DB
     */
    protected $db;

    /**
     * Undocumented function
     *
     * @param Code $code
     * @param Carbon $carbon
     * @param DB $db
     */
    public function __construct(Code $code, Carbon $carbon, DB $db)
    {
        $this->code = $code;

        $this->carbon = $carbon;
        $this->db = $db;
    }

    /**
     * Undocumented function
     *
     * @param array $attributes
     * @return void
     */
    public function sync(array $attributes) : void
    {
        $this->db->transaction(function () use ($attributes) {
            if (!$this->isSync($attributes)) {
                return;
            }

            $this->clear();

            if (isset($attributes['codes'])) {
                $this->createGlobal($attributes['codes']);
            }
        });
    }

    /**
     * [createGlobal description]
     * @param array $attributes [description]
     */
    public function createGlobal(array $attributes) : void
    {
        $this->db->transaction(function () use ($attributes) {
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
        });
    }

    /**
     * [clear description]
     * @return int [description]
     */
    public function clear() : int
    {
        return $this->db->transaction(function () {
            return $this->code->where('price_id', $this->code->price->id)->delete();
        });
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
}
