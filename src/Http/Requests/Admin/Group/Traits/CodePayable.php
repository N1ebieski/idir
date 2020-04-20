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
            $c = explode('|', $code);

            $cs[] = [
                'code' => $c[0],
                'quantity' => isset($c[1]) ?
                    ((int)$c[1] === 0 ? null : $c[1])
                    : 1
            ];
        }

        return $cs;
    }
}
