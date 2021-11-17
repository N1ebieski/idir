<?php

namespace N1ebieski\IDir\Http\Requests\Admin\Dir;

use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use N1ebieski\ICore\Models\Link;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Price;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use N1ebieski\IDir\Http\Requests\Admin\Dir\Store2Request;

class Store3Request extends Store2Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->group->isAvailable();
    }

    /**
     * Get the URL to redirect to on a validation error.
     *
     * @return string
     */
    protected function getRedirectUrl()
    {
        $url = $this->redirector->getUrlGenerator();

        return $url->route('admin.dir.create_3', [$this->group->id]);
    }

    /**
     * [prepareForValidation description]
     */
    protected function prepareForValidation(): void
    {
        if ($this->session()->has('dir')) {
            $this->merge($this->all() + $this->session()->get('dir'));
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
            [
                'backlink' => [
                    'bail',
                    'integer',
                    $this->group->backlink === Group::OBLIGATORY_BACKLINK ?
                        'required'
                        : 'nullable',
                    Rule::exists('links', 'id')->where(function ($query) {
                        $query->where('links.type', 'backlink')
                            ->whereNotExists(function ($query) {
                                $query->from('categories_models')
                                    ->whereRaw('`links`.`id` = `categories_models`.`model_id`')
                                    ->where('categories_models.model_type', 'N1ebieski\\ICore\\Models\\Link');
                            })->orWhereExists(function ($query) {
                                $query->from('categories_models')
                                    ->whereRaw('`links`.`id` = `categories_models`.`model_id`')
                                    ->where('categories_models.model_type', 'N1ebieski\\ICore\\Models\\Link')
                                    ->whereIn('categories_models.category_id', function ($query) {
                                        return $query->from('categories_closure')->select('ancestor')
                                            ->whereIn('descendant', $this->input('categories') ?? []);
                                    });
                            });
                    }),
                    'no_js_validation'
                ],
                'backlink_url' => [
                    'bail',
                    'string',
                    $this->group->backlink === Group::OBLIGATORY_BACKLINK ?
                        'required'
                        : 'nullable',
                    $this->input('url') !== null ?
                        'regex:/^' . Str::escaped($this->input('url')) . '/'
                        : 'regex:/^(https|http):\/\/([\da-z\.-]+)(\.[a-z]{2,6})/',
                    $this->group->backlink === Group::OBLIGATORY_BACKLINK && $this->has('backlink') ?
                        App::make('N1ebieski\\IDir\\Rules\\BacklinkRule', [
                            'link' => Link::find($this->input('backlink'))->url
                        ]) : null,
                    'no_js_validation'
                ]
            ],
            $this->group->prices->isNotEmpty() ?
            [
                'payment_type' => [
                    'bail',
                    'required',
                    'string',
                    Rule::in(Price::AVAILABLE),
                    'no_js_validation'
                ],
                'payment_transfer' => $this->input('payment_type') === 'transfer' ?
                [
                    'bail',
                    'required_if:payment_type,transfer',
                    'integer',
                    Rule::exists('prices', 'id')->where(function ($query) {
                        $query->where([
                            ['type', 'transfer'],
                            ['group_id', $this->group->id]
                        ]);
                    })
                ] : ['no_js_validation'],
                'payment_code_sms' => $this->input('payment_type') === 'code_sms' ?
                 [
                    'bail',
                    'required_if:payment_type,code_sms',
                    'integer',
                    Rule::exists('prices', 'id')->where(function ($query) {
                        $query->where([
                            ['type', 'code_sms'],
                            ['group_id', $this->group->id]
                        ]);
                    })
                ] : ['no_js_validation'],
                'payment_code_transfer' => $this->input('payment_type') === 'code_transfer' ?
                [
                    'bail',
                    'required_if:payment_type,code_transfer',
                    'integer',
                    Rule::exists('prices', 'id')->where(function ($query) {
                        $query->where([
                            ['type', 'code_transfer'],
                            ['group_id', $this->group->id]
                        ]);
                    })
                ] : ['no_js_validation'],
                'payment_paypal_express' => $this->input('payment_type') === 'paypal_express' ?
                [
                    'bail',
                    'required_if:payment_type,paypal_express',
                    'integer',
                    Rule::exists('prices', 'id')->where(function ($query) {
                        $query->where([
                            ['type', 'paypal_express'],
                            ['group_id', $this->group->id]
                        ]);
                    })
                ] : ['no_js_validation']
            ] : []
        );
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'backlink_url.regex' => __('validation.regex') . ' ' . Lang::get('idir::validation.backlink_url')
        ];
    }

    public function attributes()
    {
        return parent::attributes();
    }
}
