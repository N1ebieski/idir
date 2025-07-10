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

namespace N1ebieski\IDir\Overrides\Illuminate\Translation;

use Illuminate\Translation\Translator as BaseTranslator;

class Translator extends BaseTranslator
{
    /**
     * Override. In the case when Laravel not find the translation of specific namespace key,
     * verify whether the translation is in this package
     *
     * @param  string  $key
     * @param  array   $replace
     * @param  string|null  $locale
     * @param  bool  $fallback
     * @return string|array
     */
    public function get($key, array $replace = [], $locale = null, $fallback = true)
    {
        if (($old = parent::get($key, $replace, $locale, $fallback)) === $key) {
            [$namespace, $group, $item] = $this->parseKey($key);

            $new_key = "idir::{$group}.{$item}";

            return ($new = parent::get($new_key, $replace, $locale, $fallback)) !== $new_key ?
                $new : $old;
        }

        return $old;
    }
}
