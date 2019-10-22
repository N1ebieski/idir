<?php

namespace N1ebieski\IDir\Http\Requests\Web\Dir;

use N1ebieski\IDir\Http\Requests\Web\Dir\StoreFormRequest;
use N1ebieski\ICore\Http\ViewComponents\CaptchaComponent as Captcha;
use Illuminate\Validation\Rule;

class StoreSummaryRequest extends StoreFormRequest
{
    /**
     * [protected description]
     * @var Captcha
     */
    protected $captcha;

    /**
     * [__construct description]
     * @param Captcha $captcha [description]
     */
    public function __construct(Captcha $captcha)
    {
        $this->captcha = $captcha;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the URL to redirect to on a validation error.
     *
     * @return string
     */
    protected function getRedirectUrl()
    {
        $url = $this->redirector->getUrlGenerator();

        return $url->route('web.dir.create_summary', [$this->group_dir_available->id]);
    }

    protected function prepareForValidation()
    {
        if ($this->session()->has('dir')) {
            $this->merge($this->session()->get('dir'));
        }

        // if ($this->has('payment_type') && $this->input('payment_type') !== 'code_sms') {
        //     $this->merge(['code_sms' => null]);
        // }
        //
        // if ($this->has('payment_type') && $this->input('payment_type') !== 'code_transfer') {
        //     $this->merge(['code_transfer' => null]);
        // }

        parent::prepareForValidation();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),

            $this->group_dir_available->prices->isNotEmpty() ?
            [
                'payment_type' => 'bail|required|string|in:transfer,code_sms,code_transfer|no_js_validation',
                'payment_transfer' => $this->input('payment_type') === 'transfer' ?
                [
                    'bail',
                    'required_if:payment_type,transfer',
                    'integer',
                    Rule::exists('prices', 'id')->where(function($query) {
                        $query->where([
                            ['type', 'transfer'],
                            ['group_id', $this->group_dir_available->id]
                        ]);
                    }),
                    'no_js_validation'
                ] : ['no_js_validation'],
                'payment_code_sms' => $this->input('payment_type') === 'code_sms' ?
                 [
                    'bail',
                    'required_if:payment_type,code_sms',
                    'integer',
                    Rule::exists('prices', 'id')->where(function($query) {
                        $query->where([
                            ['type', 'code_sms'],
                            ['group_id', $this->group_dir_available->id]
                        ]);
                    }),
                    'no_js_validation'
                ] : ['no_js_validation'],
                'payment_code_transfer' => $this->input('payment_type') === 'code_transfer' ?
                [
                    'bail',
                    'required_if:payment_type,code_transfer',
                    'integer',
                    Rule::exists('prices', 'id')->where(function($query) {
                        $query->where([
                            ['type', 'code_transfer'],
                            ['group_id', $this->group_dir_available->id]
                        ]);
                    }),
                    'no_js_validation'
                ] : ['no_js_validation']
            ] : $this->captcha->toRules()
        );
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        if (!$validator->fails()) {
            $validator->after(function($validator) {
                $this->validate([
                    'code_sms' => $this->input('payment_type') === 'code_sms' ?
                    [
                        'bail',
                        'nullable',
                        'required_if:payment_type,code_sms',
                        'string',
                        app()->make('N1ebieski\\IDir\\Rules\\Codes\\' . ucfirst(config('idir.payment.code_sms.driver')) . '\\SMS')
                    ] : [],
                    'code_transfer' => $this->input('payment_type') === 'code_transfer' ?
                    [
                        'bail',
                        'nullable',
                        'required_if:payment_type,code_transfer',
                        'string',
                        app()->make('N1ebieski\\IDir\\Rules\\Codes\\' . ucfirst(config('idir.payment.code_transfer.driver')) . '\\Transfer')
                    ] : []
                ]);
            });
        }
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), $this->captcha->toAttributes());
    }
}
