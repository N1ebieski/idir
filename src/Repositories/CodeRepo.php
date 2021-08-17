<?php

namespace N1ebieski\IDir\Repositories;

use N1ebieski\IDir\Models\Code;

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
}
