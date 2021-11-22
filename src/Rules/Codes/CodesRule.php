<?php

namespace N1ebieski\IDir\Rules\Codes;

use Illuminate\Http\Request;
use N1ebieski\IDir\Models\Code;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Translation\Translator as Lang;

class CodesRule implements Rule
{
    /**
     * [private description]
     * @var Request
     */
    protected $request;

    /**
     * Undocumented variable
     *
     * @var Lang
     */
    protected $lang;

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param Lang $lang
     */
    public function __construct(Request $request, Lang $lang)
    {
        $this->request = $request;
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
    public function passes($attribute, $value): bool
    {
        if (($check = $this->price->makeRepo()->firstCodeByCode($value)) instanceof Code) {
            if ($check->quantity === 1) {
                $check->delete();
            } elseif ($check->quantity > 1) {
                $check->decrement('quantity');
            }

            $this->request->merge([
                'logs' => ['code' => $value]
            ]);

            return true;
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->lang->get('idir::validation.code');
    }
}
