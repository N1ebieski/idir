<?php

namespace N1ebieski\IDir\Http\Requests\Api\Dir;

use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use N1ebieski\ICore\Models\Link;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Price;
use Illuminate\Support\Facades\App;
use Mews\Purifier\Facades\Purifier;
use N1ebieski\IDir\Models\BanValue;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\IDir\Models\Category\Dir\Category;
use N1ebieski\IDir\Http\Requests\Traits\FieldsExtended;
use N1ebieski\ICore\Http\Requests\Traits\CaptchaExtended;

class UpdateRequest extends FormRequest
{
    use CaptchaExtended;
    use FieldsExtended;

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
        return $this->group
            && $this->group->isAvailable()
            && ($this->group->isPublic() || optional($this->user())->can('admin.dirs.edit'));
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

        $this->prepareContentAttribute();

        $this->prepareUrlAttribute();

        $this->prepareFieldsAttribute();
    }

    /**
     * [prepareUrl description]
     */
    protected function prepareUrlAttribute(): void
    {
        if ($this->has('url') && $this->input('url') !== null) {
            if ($this->group->url === 0) {
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
        if ($this->has('content_html')) {
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
        if ($this->has('title')) {
            $this->merge([
                'title' => Config::get('idir.dir.title_normalizer') !== null ?
                    Config::get('idir.dir.title_normalizer')($this->input('title'))
                    : $this->input('title')
            ]);
        }
    }

    /**
     * [prepareContent description]
     */
    protected function prepareContentAttribute(): void
    {
        if ($this->has('content_html')) {
            $this->merge([
                'content' => strip_tags($this->input('content_html'))
            ]);
        }
    }

    /**
     * [prepareTags description]
     */
    protected function prepareTagsAttribute(): void
    {
        if ($this->has('tags') && is_string($this->input('tags'))) {
            $this->merge([
                'tags' => explode(
                    ',',
                    Config::get('icore.tag.normalizer') !== null ?
                        Config::get('icore.tag.normalizer')($this->input('tags'))
                        : $this->input('tags')
                )
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
        clock(
            array_merge(
                [
                    'title' => 'bail|string|between:3,' . Config::get('idir.dir.max_title'),
                    'tags' => [
                        'bail',
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
                        'array',
                        'between:1,' . $this->group->max_cats
                    ],
                    'categories.*' => [
                        'bail',
                        'integer',
                        'distinct',
                        Rule::exists('categories', 'id')->where(function ($query) {
                            $query->where([
                                ['status', Category::ACTIVE],
                                ['model_type', \N1ebieski\IDir\Models\Dir::class]
                            ]);
                        })
                    ],
                    'content_html' => [
                        'bail',
                        'string',
                        'between:' . Config::get('idir.dir.min_content') . ',' . Config::get('idir.dir.max_content'),
                        !empty($this->bans_words) ? 'not_regex:/(.*)(\s|^)(' . $this->bans_words . ')(\s|\.|,|\?|$)(.*)/i' : null
                    ],
                    'notes' => 'bail|nullable|string|between:3,255',
                    'url' => [
                        'bail',
                        ($this->group->url === Group::OBLIGATORY_URL) ?
                            (empty(optional($this->dir)->url) ? 'required' : 'sometimes')
                            : 'nullable',
                        'string',
                        'regex:/^(https|http):\/\/([\da-z\.-]+)(\.[a-z]{2,6})\/?$/',
                        !empty($this->bans_urls) ? 'not_regex:/(' . $this->bans_urls . ')/i' : null,
                        App::make(\N1ebieski\IDir\Rules\UniqueUrlRule::class, [
                            'table' => 'dirs',
                            'column' => 'url',
                            'ignore' => optional($this->dir)->id
                        ])
                    ],
                    'backlink' => [
                        'bail',
                        'integer',
                        ($this->group->backlink === Group::OBLIGATORY_BACKLINK) ?
                            (empty($this->dir->backlink->url) ? 'required' : 'sometimes')
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
                        })
                    ],
                    'backlink_url' => [
                        'bail',
                        'string',
                        $this->group->backlink === Group::OBLIGATORY_BACKLINK ?
                            (empty($this->dir->backlink->url) ? 'required' : 'sometimes')
                            : 'nullable',
                        $this->input('url') !== null ?
                            'regex:/^' . Str::escaped($this->input('url')) . '/'
                            : 'regex:/^(https|http):\/\/([\da-z\.-]+)(\.[a-z]{2,6})/',
                        $this->group->backlink === Group::OBLIGATORY_BACKLINK && $this->has('backlink') ?
                            App::make('N1ebieski\\IDir\\Rules\\BacklinkRule', [
                                'link' => Link::find($this->input('backlink'))->url
                            ]) : null
                    ]
                ],
                optional($this->user())->can('admin.dirs.edit') ?
                [
                    'user' => 'bail|nullable|integer|exists:users,id',
                ] : [],
                optional($this->dir)->isPayment($this->group->id) && $this->group->prices->isNotEmpty() ?
                [
                    'payment_type' => [
                        'bail',
                        'required',
                        'string',
                        Rule::in(Price::AVAILABLE)
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
                    ] : [],
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
                    ] : [],
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
                    ] : [],
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
                    ] : []
                ] : (optional($this->user())->can('admin.dirs.edit') ? [] : $this->prepareCaptchaRules()),
                $this->prepareFieldsRules()
            )
        );

        return array_merge(
            [
                'title' => 'bail|string|between:3,' . Config::get('idir.dir.max_title'),
                'tags' => [
                    'bail',
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
                    'array',
                    'between:1,' . $this->group->max_cats
                ],
                'categories.*' => [
                    'bail',
                    'integer',
                    'distinct',
                    Rule::exists('categories', 'id')->where(function ($query) {
                        $query->where([
                            ['status', Category::ACTIVE],
                            ['model_type', \N1ebieski\IDir\Models\Dir::class]
                        ]);
                    })
                ],
                'content_html' => [
                    'bail',
                    'string',
                    'between:' . Config::get('idir.dir.min_content') . ',' . Config::get('idir.dir.max_content'),
                    !empty($this->bans_words) ? 'not_regex:/(.*)(\s|^)(' . $this->bans_words . ')(\s|\.|,|\?|$)(.*)/i' : null
                ],
                'notes' => 'bail|nullable|string|between:3,255',
                'url' => [
                    'bail',
                    ($this->group->url === Group::OBLIGATORY_URL) ?
                        (empty(optional($this->dir)->url) ? 'required' : 'sometimes')
                        : 'nullable',
                    'string',
                    'regex:/^(https|http):\/\/([\da-z\.-]+)(\.[a-z]{2,6})\/?$/',
                    !empty($this->bans_urls) ? 'not_regex:/(' . $this->bans_urls . ')/i' : null,
                    App::make(\N1ebieski\IDir\Rules\UniqueUrlRule::class, [
                        'table' => 'dirs',
                        'column' => 'url',
                        'ignore' => optional($this->dir)->id
                    ])
                ],
                'backlink' => [
                    'bail',
                    'integer',
                    ($this->group->backlink === Group::OBLIGATORY_BACKLINK) ?
                        (empty(optional(optional($this->dir)->backlink)->url) ? 'required' : 'sometimes')
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
                    })
                ],
                'backlink_url' => [
                    'bail',
                    'string',
                    $this->group->backlink === Group::OBLIGATORY_BACKLINK ?
                        (empty(optional(optional($this->dir)->backlink)->url) ? 'required' : 'sometimes')
                        : 'nullable',
                    $this->input('url') !== null ?
                        'regex:/^' . Str::escaped($this->input('url')) . '/'
                        : 'regex:/^(https|http):\/\/([\da-z\.-]+)(\.[a-z]{2,6})/',
                    $this->group->backlink === Group::OBLIGATORY_BACKLINK && $this->has('backlink') ?
                        App::make('N1ebieski\\IDir\\Rules\\BacklinkRule', [
                            'link' => Link::find($this->input('backlink'))->url
                        ]) : null
                ]
            ],
            optional($this->user())->can('admin.dirs.edit') ?
            [
                'user' => 'bail|nullable|integer|exists:users,id',
            ] : [],
            optional($this->dir)->isPayment($this->group->id) && $this->group->prices->isNotEmpty() ?
            [
                'payment_type' => [
                    'bail',
                    'required',
                    'string',
                    Rule::in(Price::AVAILABLE)
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
                ] : [],
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
                ] : [],
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
                ] : [],
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
                ] : []
            ] : (optional($this->user())->can('admin.dirs.edit') ? [] : $this->prepareCaptchaRules()),
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
            'backlink_url.regex' => Lang::get('validation.regex') . ' ' . Lang::get('idir::validation.backlink_url')
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
                'example' => []
            ],
            'tags.*' => [
                'example' => []
            ],
            'url' => [
                'description' => 'Unique website url with http/https protocol.'
            ],
            'title' => [
                'example' => ''
            ],
            'content_html' => [
                'description' => 'Description.',
                'example' => ''
            ],
            'notes' => [
                'description' => 'Additional information for the moderator.',
                'example' => ''
            ],
            'backlink' => [
                'description' => 'ID of the selected backlink.',
                'example' => ''
            ],
            'backlink_url' => [
                'description' => 'Url with http/https protocol to backlink.',
                'example' => ''
            ]
        ];
    }
}
