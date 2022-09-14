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

namespace N1ebieski\IDir\Http\Requests\Admin\Price\Traits;

trait HasCodes
{
    /**
     * [prepareCodes description]
     * @param  string $codes [description]
     * @return array         [description]
     */
    protected static function prepareCodes(string $codes): array
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
