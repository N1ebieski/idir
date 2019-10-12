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
                'payment_type' => 'bail|required|string|in:transfer,auto_sms',
                'payment_transfer' => [
                    'bail',
                    'required_if:payment_type,transfer',
                    'integer',
                    Rule::exists('prices', 'id')->where(function($query) {
                        $query->where([
                            ['type', 'transfer'],
                            ['group_id', $this->group_dir_available->id]
                        ]);
                    })
                ],
                'payment_auto_sms' => [
                    'bail',
                    'required_if:payment_type,auto_sms',
                    'integer',
                    Rule::exists('prices', 'id')->where(function($query) {
                        $query->where([
                            ['type', 'auto_sms'],
                            ['group_id', $this->group_dir_available->id]
                        ]);
                    })
                ]
            ] : $this->captcha->toRules()
        );
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), $this->captcha->toAttributes());
    }
}
