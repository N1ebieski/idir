<?php

namespace N1ebieski\IDir\Translation;

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
