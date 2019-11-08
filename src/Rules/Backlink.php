<?php

namespace N1ebieski\IDir\Rules;

use Illuminate\Contracts\Validation\Rule;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Str;

/**
 * [Backlink description]
 */
class Backlink implements Rule
{
    /**
     * [private description]
     * @var string
     */
    protected $link;

    /**
     * Create a new rule instance.
     *
     * @param string $link [description]
     * @return void
     */
    public function __construct(string $link)
    {
        $this->link = $link;
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
        $client = new GuzzleClient(['timeout' => 10.0]);

        try {
            $response = $client->get($value);
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            return false;
        }

        return preg_match('/<a\s((?:(?!nofollow|>).)*)href=([\"\']??)' . Str::escaped($this->link) . '([\"\']??)((?:(?!nofollow|>).)*)>(.*)<\/a>/siU',
            $response->getBody()->getContents());
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('idir::validation.backlink');
    }
}
