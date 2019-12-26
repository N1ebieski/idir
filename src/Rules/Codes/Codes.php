<?php

namespace N1ebieski\IDir\Rules\Codes;

use Illuminate\Contracts\Validation\Rule;
use N1ebieski\IDir\Models\Code;
use Illuminate\Http\Request;

/**
 * [Recaptcha_v2 description]
 */
class Codes implements Rule
{
    /**
     * [private description]
     * @var Request
     */
    protected $request;

    /**
     * [__construct description]
     * @param Request  $request  [description]
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
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
    public function passes($attribute, $value) : bool
    {
        if (($check = $this->price->makeRepo()->firstCodeByCode($value)) instanceof Code) {
            if ($check->quantity === 1) {
                $check->delete();
            } else if ($check->quantity > 1) {
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
        return trans('idir::validation.code');
    }
}
