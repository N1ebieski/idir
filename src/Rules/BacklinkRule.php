<?php

namespace N1ebieski\IDir\Rules;

use Illuminate\Support\Str;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Translation\Translator as Lang;

/**
 * [Backlink description]
 */
class BacklinkRule implements Rule
{
    /**
     * [private description]
     * @var string
     */
    protected $link;

    /**
     * [protected description]
     * @var GuzzleClient
     */
    protected $guzzle;

    /**
     * Undocumented variable
     *
     * @var Lang
     */
    protected $lang;

    /**
     * Undocumented function
     *
     * @param string $link
     * @param GuzzleClient $guzzle
     * @param Lang $lang
     */
    public function __construct(string $link, GuzzleClient $guzzle, Lang $lang)
    {
        $this->link = $link;

        $this->guzzle = $guzzle;
        $this->lang = $lang;
    }

    /**
     * [validate description]
     * @param  [type] $attribute  [description]
     * @param  [type] $value      [description]
     * @param  [type] $parameters [description]
     * @param  [type] $validator  [description]
     * @return [type]             [description]
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
            $response = $this->guzzle->request('GET', $value);
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            return false;
        }

        return preg_match(
            '/<a\s((?:(?!nofollow|>).)*)href=([\"\']??)' . Str::escaped($this->link) . '([\"\']??)((?:(?!nofollow|>).)*)>(.*)<\/a>/siU',
            $response->getBody()->getContents()
        );
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
