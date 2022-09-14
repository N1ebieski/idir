<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\IDir\Services\Code;

use Throwable;
use Illuminate\Support\Carbon;
use N1ebieski\IDir\Models\Code;
use Illuminate\Database\DatabaseManager as DB;

class CodeService
{
    /**
     * Undocumented function
     *
     * @param Code $code
     * @param Carbon $carbon
     * @param DB $db
     */
    public function __construct(
        protected Code $code,
        protected Carbon $carbon,
        protected DB $db
    ) {
        //
    }

    /**
     * Undocumented function
     *
     * @param array $attributes
     * @return void
     */
    public function sync(array $attributes): void
    {
        $this->db->transaction(function () use ($attributes) {
            if (!$this->isSync($attributes)) {
                return;
            }

            $this->clear($attributes['price']);

            if (array_key_exists('codes', $attributes)) {
                $this->createGlobal([
                    'price' => $attributes['price'],
                    'codes' => $attributes['codes']
                ]);
            }
        });
    }

    /**
     * [createGlobal description]
     * @param array $attributes [description]
     */
    public function createGlobal(array $attributes): void
    {
        $this->db->transaction(function () use ($attributes) {
            $codes = [];

            foreach ($attributes['codes'] as $attribute) {
                // Create attributes manually, no within model because multiple
                // models may be huge performance impact
                $codes[] = [
                    'price_id' => $attributes['price'],
                    'code' => $attribute['code'],
                    'quantity' => $attribute['quantity'],
                    'created_at' => $this->carbon->now(),
                    'updated_at' => $this->carbon->now()
                ];
            }

            $this->code->newQuery()->insertOrIgnore($codes);
        });
    }

    /**
     *
     * @param int $priceId
     * @return int
     * @throws Throwable
     */
    public function clear(int $priceId): int
    {
        return $this->db->transaction(function () use ($priceId) {
            return $this->code->where('price_id', $priceId)->delete();
        });
    }

    /**
     * Undocumented function
     *
     * @param array $attributes
     * @return boolean
     */
    protected function isSync(array $attributes): bool
    {
        return isset($attributes['sync']);
    }
}
