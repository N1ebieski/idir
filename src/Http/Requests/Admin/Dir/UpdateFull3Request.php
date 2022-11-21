<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\IDir\Http\Requests\Admin\Dir;

use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use N1ebieski\ICore\Models\Link;
use N1ebieski\IDir\Models\Group;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\ValidatedInput;
use N1ebieski\ICore\ValueObjects\Link\Type as LinkType;
use N1ebieski\IDir\ValueObjects\Price\Type as PriceType;
use N1ebieski\IDir\Http\Requests\Admin\Dir\UpdateFull2Request;

/**
 * @property Group $group
 */
class UpdateFull3Request extends UpdateFull2Request
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

        return $url->route('admin.dir.edit_full_3', [$this->dir->id, $this->group->id]);
    }

    /**
     * [prepareForValidation description]
     */
    protected function prepareForValidation(): void
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
        /** @var Link */
        $link = Link::find($this->input('backlink'));

        return array_merge(
            parent::rules(),
            [
                'user' => 'bail|required|integer|exists:users,id',
                'backlink' => [
                    'bail',
                    'integer',
                    $this->group->backlink->isActive() ?
                        'required'
                        : 'nullable',
                    Rule::exists('links', 'id')->where(function ($query) {
                        $query->where('links.type', LinkType::BACKLINK)
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
                    $this->group->backlink->isActive() ?
                        'required'
                        : 'nullable',
                    $this->input('url') !== null ?
                        'regex:/^' . Str::escaped($this->input('url')) . '/'
                        : 'regex:/^(https|http):\/\/([\da-z\.-]+)(\.[a-z]{2,7})/',
                    $this->group->backlink->isActive() && $this->has('backlink') ?
                        App::make('N1ebieski\\IDir\\Rules\\BacklinkRule', [
                            'link' => $link->url
                        ]) : null,
                    'no_js_validation'
                ]
            ],
            $this->dir->isPayment($this->group->id) && $this->group->prices->isNotEmpty() ?
            [
                'payment_type' => [
                    'bail',
                    'required',
                    'string',
                    Rule::in(PriceType::getAvailable()),
                    'no_js_validation'
                ],
                'payment_transfer' => $this->input('payment_type') === PriceType::TRANSFER ?
                [
                    'bail',
                    'required_if:payment_type,' . PriceType::TRANSFER,
                    'integer',
                    Rule::exists('prices', 'id')->where(function ($query) {
                        $query->where([
                            ['type', PriceType::TRANSFER],
                            ['group_id', $this->group->id]
                        ]);
                    })
                ] : ['no_js_validation'],
                'payment_code_sms' => $this->input('payment_type') === PriceType::CODE_SMS ?
                 [
                    'bail',
                    'required_if:payment_type,' . PriceType::CODE_SMS,
                    'integer',
                    Rule::exists('prices', 'id')->where(function ($query) {
                        $query->where([
                            ['type', PriceType::CODE_SMS],
                            ['group_id', $this->group->id]
                        ]);
                    })
                ] : ['no_js_validation'],
                'payment_code_transfer' => $this->input('payment_type') === PriceType::CODE_TRANSFER ?
                [
                    'bail',
                    'required_if:payment_type,' . PriceType::CODE_TRANSFER,
                    'integer',
                    Rule::exists('prices', 'id')->where(function ($query) {
                        $query->where([
                            ['type', PriceType::CODE_TRANSFER],
                            ['group_id', $this->group->id]
                        ]);
                    })
                ] : ['no_js_validation'],
                'payment_paypal_express' => $this->input('payment_type') === PriceType::PAYPAL_EXPRESS ?
                [
                    'bail',
                    'required_if:payment_type,' . PriceType::PAYPAL_EXPRESS,
                    'integer',
                    Rule::exists('prices', 'id')->where(function ($query) {
                        $query->where([
                            ['type', PriceType::PAYPAL_EXPRESS],
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
            // @phpstan-ignore-next-line
            'backlink_url.regex' => __('validation.regex') . ' ' . Lang::get('idir::validation.backlink_url')
        ];
    }

    /**
     * Get a validated input container for the validated input.
     *
     * @param  array|null  $keys
     * @return \Illuminate\Support\ValidatedInput|array
     */
    public function safe(array $keys = null)
    {
        if ($this->has('payment_type')) {
            $types = [];

            foreach (PriceType::getAvailable() as $type) {
                $types[] = "payment_{$type}";
            }

            $safe = new ValidatedInput(parent::safe($keys)->except($types));

            return $safe->merge([
                'price' => $this->input("payment_{$this->input('payment_type')}")
            ]);
        }

        return parent::safe($keys);
    }
}
