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

namespace N1ebieski\IDir\Rules;

use RuntimeException;
use Illuminate\Support\Str;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Translation\Translator as Lang;
use Illuminate\Contracts\Container\BindingResolutionException;
use N1ebieski\IDir\Http\Clients\DirBacklink\DirBacklinkClient;

class BacklinkRule implements Rule
{
    /**
     * Undocumented function
     *
     * @param DirBacklinkClient $client
     * @param Lang $lang
     * @param string $link
     */
    public function __construct(
        protected DirBacklinkClient $client,
        protected Lang $lang,
        protected string $link
    ) {
        //
    }

    /**
     *
     * @param mixed $attribute
     * @param mixed $value
     * @param mixed $parameters
     * @param mixed $validator
     * @return bool
     * @throws BindingResolutionException
     * @throws RuntimeException
     */
    public function validate($attribute, $value, $parameters, $validator)
    {
        return $this->passes($attribute, $value);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        try {
            $response = $this->client->show($value);
        } catch (\N1ebieski\IDir\Exceptions\DirBacklink\TransferException $e) {
            return false;
        }

        return preg_match(
            '/<a\s((?:(?!nofollow|>).)*)href=([\"\']??)' . Str::escaped($this->link) . '([\"\']??)((?:(?!nofollow|>).)*)>(.*)<\/a>/siU',
            $response->getBody()->getContents()
        ) ? true : false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->lang->get('idir::validation.backlink');
    }
}
