<?php

namespace N1ebieski\IDir\Http\Requests\Admin\Group\Traits;

/**
 * [trait description]
 */
trait CodePayable
{
    /**
     * [prepareCodes description]
     * @param  string $codes [description]
     * @return array         [description]
     */
    protected static function prepareCodes(string $codes) : array
    {
        $codes = explode("\r\n", $codes);

        foreach ($codes as $code) {
            $_code = explode('|', $code);

            $_codes[] = [
                'code' => $_code[0],
                'quantity' => (int)($_code[1] ?? 1)
            ];
        }

        return $_codes;
    }
}
