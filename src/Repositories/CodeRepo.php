<?php

namespace N1ebieski\IDir\Repositories;

use N1ebieski\IDir\Models\Code;

/**
 * [CodeRepo description]
 */
class CodeRepo
{
    /**
     * [private description]
     * @var Code
     */
    protected $code;

    /**
     * [__construct description]
     * @param Code $code [description]
     */
    public function __construct(Code $code)
    {
        $this->code = $code;
    }

    /**
     * [firstByCodeAndPriceId description]
     * @param  string $code [description]
     * @param  int    $id   [description]
     * @return Code|null       [description]
     */
    public function firstByCodeAndPriceId(string $code, int $id) : ?Code
    {
        return $this->code->where([
                ['code', $code],
                ['price_id', $id]
            ])->first();
    }
}
