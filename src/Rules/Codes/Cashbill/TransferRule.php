<?php

namespace N1ebieski\IDir\Rules\Codes\Cashbill;

use Illuminate\Http\Request;
use N1ebieski\IDir\Models\Price;
use N1ebieski\IDir\Rules\Codes\CodesRule;
use Illuminate\Contracts\Translation\Translator as Lang;
use N1ebieski\IDir\Utils\Cashbill\Codes\TransferUtil as Cashbill;

/**
 * [Recaptcha_v2 description]
 */
class TransferRule extends CodesRule
{
    /**
     * [private description]
     * @var Price
     */
    protected $price;

    /**
     * [private description]
     * @var Cashbill
     */
    protected $cashbill;

    /**
     * Undocumented function
     *
     * @param Price $price
     * @param Request $request
     * @param Lang $lang
     * @param Cashbill $cashbill
     */
    public function __construct(Price $price, Request $request, Lang $lang, Cashbill $cashbill)
    {
        parent::__construct($request, $lang);

        $this->price = $price;

        $this->cashbill = $cashbill;

        $this->makePrice();
    }

    /**
     * Undocumented function
     *
     * @return Price
     */
    protected function makePrice() : Price
    {
        return $this->price = $this->price->find($this->request->input('payment_code_transfer'));
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
            $this->cashbill->authorize([
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
        return $this->lang->get('idir::validation.code');
    }
}