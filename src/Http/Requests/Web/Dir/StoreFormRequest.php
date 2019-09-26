<?php

namespace N1ebieski\IDir\Http\Requests\Web\Dir;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Mews\Purifier\Facades\Purifier;

class StoreFormRequest extends FormRequest
{
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
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->prepareTagsAttribute();

        $this->prepareContentHtmlAttribute();

        $this->prepareContentAttribute();

        $this->prepareUrlAttribute();
    }

    /**
     * [prepareUrl description]
     */
    protected function prepareUrlAttribute() : void
    {
        if ($this->has('url')) {
            if ($this->group_dir_available->url === 0) {
                $this->merge(['url' => null]);
            } else {
                $this->merge(['url' => preg_replace('/(\/)$/', '', $this->input('url'))]);
            }
        }
    }

    /**
     * [prepareContentHtml description]
     */
    protected function prepareContentHtmlAttribute() : void
    {
        if ($this->has('content_html')) {
            if ($this->group_dir_available->privileges->contains('name', 'additional options for editing content')) {
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
     * [prepareContent description]
     */
    protected function prepareContentAttribute() : void
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
    protected function prepareTagsAttribute() : void
    {
        if ($this->has('tags') && is_string($this->input('tags'))) {
            $this->merge([
                'tags' => explode(',', $this->input('tags'))
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
        return [
            'title' => 'bail|required|string|between:3,255',
            'tags' => [
                'bail',
                'nullable',
                'array',
                'between:0,' . config('idir.dir.max_tags'),
                'no_js_validation'
            ],
            'tags.*' => 'bail|min:3|max:30|alpha_num_spaces',
            'categories' => [
                'bail',
                'required',
                'array',
                'between:1,' . $this->group_dir_available->max_cats,
                'no_js_validation'
            ],
            'categories.*' => [
                'bail',
                'integer',
                'distinct',
                Rule::exists('categories', 'id')->where(function($query) {
                    $query->where([
                        ['status', 1],
                        ['model_type', 'N1ebieski\\IDir\\Models\\Dir']
                    ]);
                })
            ],
            // Wyłącznie na potrzeby jsvalidation
            'content_html' => [
                'bail',
                'required',
                'string',
                'between:' . config('idir.dir.min_content') . ',' . config('idir.dir.max_content')
            ],
            'content' => [
                'bail',
                'required',
                'string',
                'between:' . config('idir.dir.min_content') . ',' . config('idir.dir.max_content')
            ],
            'notes' => 'bail|nullable|string|between:3,255',
            'url' => [
                'bail',
                ($this->group_dir_available->url === 2) ? 'required' : 'nullable',
                'string',
                'regex:/^(https|http):\/\/([\da-z\.-]+)(\.[a-z]{2,6})\/?$/',
                'unique:dirs,url'
            ]
        ];
    }
}
