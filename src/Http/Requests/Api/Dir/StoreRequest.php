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

namespace N1ebieski\IDir\Http\Requests\Api\Dir;

use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use N1ebieski\IDir\Models\Link;
use N1ebieski\IDir\Models\Group;
use Illuminate\Support\Facades\App;
use Mews\Purifier\Facades\Purifier;
use N1ebieski\IDir\Models\BanValue;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ValidatedInput;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as Collect;
use N1ebieski\ICore\ValueObjects\Category\Status;
use N1ebieski\IDir\Http\Requests\Traits\HasFields;
use N1ebieski\ICore\Http\Requests\Traits\HasCaptcha;
use N1ebieski\ICore\ValueObjects\Link\Type as LinkType;
use N1ebieski\IDir\ValueObjects\Price\Type as PriceType;

/**
 * @property Group $group
 */
class StoreRequest extends FormRequest
{
    use HasCaptcha;
    use HasFields;

    /**
     * [private description]
     * @var string
     */
    protected $bans_words;

    /**
     * [private description]
     * @var string
     */
    protected $bans_urls;

    /**
     * [__construct description]
     * @param BanValue $banValue [description]
     */
    public function __construct(BanValue $banValue)
    {
        parent::__construct();

        $this->bans_words = $banValue->makeCache()->rememberAllWordsAsString();

        $this->bans_urls = $banValue->makeCache()->rememberAllUrlsAsString();
    }

    /**
     * [getFields description]
     * @return Collection [description]
     */
    public function getFields(): Collection
    {
        return $this->group->fields;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->group->isAvailable()
            && ($this->group->visible->isActive() || optional($this->user())->can('admin.dirs.create'));
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->prepareTagsAttribute();

        $this->prepareTitleAttribute();

        $this->prepareContentHtmlAttribute();

        $this->prepareUrlAttribute();

        $this->prepareFieldsAttribute();
    }

    /**
     * [prepareUrl description]
     */
    protected function prepareUrlAttribute(): void
    {
        if ($this->has('url') && $this->input('url') !== null) {
            if ($this->group->url->isInactive()) {
                $this->merge(['url' => null]);
            } else {
                $this->merge(['url' => preg_replace('/(\/)$/', '', $this->input('url'))]);
            }
        }
    }

    /**
     * [prepareContentHtml description]
     */
    protected function prepareContentHtmlAttribute(): void
    {
        if ($this->has('content_html') && is_string($this->input('content_html'))) {
            if ($this->group->privileges->contains('name', 'additional options for editing content')) {
                $this->merge([
                    'content_html' => Purifier::clean($this->input('content_html'), 'dir')
                ]);
            } else {
                $this->merge([
                    'content_html' => strip_tags($this->input('content_html'))
                ]);
            }
        }
    }

    /**
     * [prepareTitle description]
     */
    protected function prepareTitleAttribute(): void
    {
        if ($this->has('title') && is_string($this->input('title'))) {
            $this->merge([
                'title' => Config::get('idir.dir.title_normalizer') !== null ?
                    Config::get('idir.dir.title_normalizer')($this->input('title'))
                    : $this->input('title')
            ]);
        }
    }

    /**
     * [prepareTags description]
     */
    protected function prepareTagsAttribute(): void
    {
        if ($this->has('tags') && is_array($this->input('tags'))) {
            $this->merge([
                'tags' => Collect::make($this->input('tags'))
                    ->map(function ($tag) {
                        return Config::get('icore.tag.normalizer') !== null ?
                            Config::get('icore.tag.normalizer')($tag)
                            : $tag;
                    })
                    ->toArray()
            ]);
        }
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
            [
                'title' => 'bail|required|string|between:3,' . Config::get('idir.dir.max_title'),
                'tags' => [
                    'bail',
                    'nullable',
                    'array',
                    'between:0,' . Config::get('idir.dir.max_tags')
                ],
                'tags.*' => [
                    'bail',
                    'min:3',
                    'max:' . Config::get('icore.tag.max_chars'),
                    'alpha_num_spaces'
                ],
                'categories' => [
                    'bail',
                    'required',
                    'array',
                    'between:1,' . $this->group->max_cats
                ],
                'categories.*' => [
                    'bail',
                    'required',
                    'integer',
                    'distinct',
                    Rule::exists('categories', 'id')->where(function ($query) {
                        $query->where([
                            ['status', Status::ACTIVE],
                            ['model_type', \N1ebieski\IDir\Models\Dir::class]
                        ]);
                    })
                ],
                'content_html' => [
                    'bail',
                    'required',
                    'string',
                    'between:' . Config::get('idir.dir.min_content') . ',' . Config::get('idir.dir.max_content'),
                    !empty($this->bans_words) ? 'not_regex:/(.*)(\s|^)(' . $this->bans_words . ')(\s|\.|,|\?|$)(.*)/i' : null
                ],
                'notes' => 'bail|nullable|string|between:3,255',
                'url' => [
                    'bail',
                    $this->group->url->isActive() ? 'required' : 'nullable',
                    'string',
                    'regex:/^(https|http):\/\/([\da-z\.-]+)(\.[a-z]{2,6})\/?$/',
                    !empty($this->bans_urls) ? 'not_regex:/(' . $this->bans_urls . ')/i' : null,
                    App::make(\N1ebieski\IDir\Rules\UniqueUrlRule::class, [
                        'table' => 'dirs',
                        'column' => 'url'
                    ])
                ],
                'backlink' => [
                    'bail',
                    $this->group->backlink->isActive() ?
                        'required'
                        : 'nullable',
                    'integer',
                    Rule::exists('links', 'id')->where(function ($query) {
                        $query->where('links.type', LinkType::BACKLINK)
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
                    })
                ],
                'backlink_url' => [
                    'bail',
                    'string',
                    $this->group->backlink->isActive() ?
                        'required'
                        : 'nullable',
                    $this->input('url') !== null ?
                        'regex:/^' . Str::escaped($this->input('url')) . '/'
                        : 'regex:/^(https|http):\/\/([\da-z\.-]+)(\.[a-z]{2,6})/',
                    $this->group->backlink->isActive() && $this->has('backlink') ?
                        App::make('N1ebieski\\IDir\\Rules\\BacklinkRule', [
                            'link' => $link->url
                        ]) : null
                ]
            ],
            !$this->user() ?
            [
                'email' => 'bail|required|string|email|unique:users,email'
            ] : [],
            $this->group->prices->isNotEmpty() ?
            [
                'payment_type' => [
                    'bail',
                    'required',
                    'string',
                    Rule::in(PriceType::getAvailable()),
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
                ] : [],
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
                ] : [],
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
                ] : [],
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
                ] : []
            ] : (optional($this->user())->can('admin.dirs.create') ? [] : $this->prepareCaptchaRules()),
            $this->prepareFieldsRules()
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
            'content.not_regex' => Lang::get('icore::validation.not_regex_contains', [
                'words' => str_replace('|', ', ', $this->bans_words)
            ]),
            'url.not_regex' => 'This address url is banned.',
            // @phpstan-ignore-next-line
            'backlink_url.regex' => Lang::get('validation.regex') . ' ' . Lang::get('idir::validation.backlink_url'),
            // @phpstan-ignore-next-line
            'email.unique' => strip_tags(Lang::get('idir::validation.email'))
        ];
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function bodyParameters(): array
    {
        return [
            'categories.*' => [
                'description' => 'Array containing category IDs.',
            ],
            'url' => [
                'description' => 'Unique website url with http/https protocol.'
            ],
            'title' => [
                'example' => 'Lorem ipsum dolor sit amet'
            ],
            'content_html' => [
                'description' => 'Description.',
                'example' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'
            ],
            'notes' => [
                'description' => 'Additional information for the moderator.',
                'example' => ''
            ],
            'backlink' => [
                'description' => 'ID of the selected backlink.',
                'example' => 'No-example'
            ],
            'backlink_url' => [
                'description' => 'Url with http/https protocol to backlink.',
                'example' => 'No-example'
            ],
            'g-recaptcha-response' => [
                'description' => $this->prepareCaptchaBodyParameters()['g-recaptcha-response']['description'] . ' Only required for free groups.'
            ],
            'key' => [
                'description' => $this->prepareCaptchaBodyParameters()['key']['description'] . ' Only required for free groups.'
            ],
            'captcha' => [
                'description' => $this->prepareCaptchaBodyParameters()['captcha']['description'] . ' Only required for free groups.'
            ],
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
