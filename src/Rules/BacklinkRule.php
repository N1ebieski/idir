<?php

namespace N1ebieski\IDir\Rules;

use Illuminate\Support\Str;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Translation\Translator as Lang;
use N1ebieski\IDir\Http\Clients\Dir\CheckBacklinkClient;

class BacklinkRule implements Rule
{
    /**
     * [private description]
     * @var string
     */
    protected $link;

    /**
     * [protected description]
     * @var CheckBacklinkClient
     */
    protected $client;

    /**
     * Undocumented variable
     *
     * @var Lang
     */
    protected $lang;

    /**
     * Undocumented function
     *
     * @param CheckBacklinkClient $client
     * @param Lang $lang
     * @param string $link
     */
    public function __construct(CheckBacklinkClient $client, Lang $lang, string $link)
    {
        $this->link = $link;

        $this->client = $client;
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
            $this->client->setUrl($value)->request();
        } catch (\N1ebieski\IDir\Exceptions\Dir\TransferException $e) {
            return false;
        }

        return preg_match(
            '/<a\s((?:(?!nofollow|>).)*)href=([\"\']??)' . Str::escaped($this->link) . '([\"\']??)((?:(?!nofollow|>).)*)>(.*)<\/a>/siU',
            $this->client->getContents()
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
