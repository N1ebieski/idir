<?php

namespace N1ebieski\IDir\Http\Requests\Web\Dir;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use N1ebieski\IDir\Http\Requests\Web\Dir\Update2Request;
use N1ebieski\ICore\Http\ViewComponents\CaptchaComponent as Captcha;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use N1ebieski\ICore\Models\Link;
use N1ebieski\IDir\Models\Group;

class Update3Request extends Update2Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $check = $this->group->isPublic();

        return $this->dir->isGroup($this->group->id) ?
            $check : $check && $this->group->isAvailable();
    }

    /**
     * Get the URL to redirect to on a validation error.
     *
     * @return string
     */
    protected function getRedirectUrl()
    {
        $url = $this->redirector->getUrlGenerator();

        return $url->route('web.dir.edit_3', [$this->dir->id, $this->group->id]);
    }

    /**
     * [prepareForValidation description]
     */
    protected function prepareForValidation() : void
    {
        if ($this->session()->has("dirId.{$this->dir->id}")) {
            $this->merge($this->all() + $this->session()->get("dirId.{$this->dir->id}"));
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
                                    ->whereRaw('links.id = categories_models.model_id')
                                    ->where('categories_models.model_type', 'N1ebieski\\ICore\\Models\\Link');
                            })->orWhereExists(function ($query) {
                                $query->from('categories_models')
                                    ->whereRaw('links.id = categories_models.model_id')
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
                    $this->group->backlink === 2 && $this->has('backlink') ?
                        App::make('N1ebieski\\IDir\\Rules\\BacklinkRule', [
                            'link' => Link::find($this->input('backlink'))->url
                        ]) : null,
                    'no_js_validation'
                ]
            ],
            $this->dir->isPayment($this->group->id) && $this->group->prices->isNotEmpty() ?
            [
                'payment_type' => 'bail|required|string|in:transfer,code_sms,code_transfer|no_js_validation',
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
                ] : ['no_js_validation']
            ] : App::make(Captcha::class)->toRules()
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
        return array_merge(parent::attributes(), App::make(Captcha::class)->toAttributes());
    }
}
