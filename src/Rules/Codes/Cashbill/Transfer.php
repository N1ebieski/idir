<?php

namespace N1ebieski\IDir\Rules\Codes\Cashbill;

use N1ebieski\IDir\Utils\Cashbill\Codes\Transfer as Cashbill;
use N1ebieski\IDir\Models\Price;
use Illuminate\Http\Request;
use N1ebieski\IDir\Rules\Codes\Codes;

/**
 * [Recaptcha_v2 description]
 */
class Transfer extends Codes
{
    /**
     * [private description]
     * @var Price
     */
    protected $price;

    /**
     * [private description]
     * @var Request
     */
    protected $request;

    /**
     * [private description]
     * @var Cashbill
     */
    protected $cashbill;

    /**
     * [__construct description]
     * @param Price    $price    [description]
     * @param Request  $request  [description]
     * @param Cashbill $cashbill [description]
     */
    public function __construct(Price $price, Request $request, Cashbill $cashbill)
    {
        $this->cashbill = $cashbill;
        $this->price = $price->find($request->input('payment_code_transfer'));
        $this->request = $request;

        parent::__construct($request);
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
        if (parent::passes($attribute, $value) === true) {
            return true;
        }

        try {
            $this->cashbill->verify([
                'code' => $value,
                'id' => $this->price->code
            ]);
        } catch (\Exception $e) {
            return false;
        }

        $this->request->merge([
            'logs' => (array)$this->cashbill->getResponse() + ['code' => $value]
        ]);

        return true;
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
