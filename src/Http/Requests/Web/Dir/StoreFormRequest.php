<?php

namespace N1ebieski\IDir\Http\Requests\Web\Dir;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Mews\Purifier\Facades\Purifier;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

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
    protected function prepareForValidation() : void
    {
        $this->prepareTagsAttribute();

        $this->prepareContentHtmlAttribute();

        $this->prepareContentAttribute();

        $this->prepareUrlAttribute();

        $this->prepareFieldsAttribute();
    }

    /**
     * [prepareFieldsAttribute description]
     */
    protected function prepareFieldsAttribute() : void
    {
        if (!$this->has('field') && !is_array($this->input('field'))) {
            return;
        }

        foreach ($this->group_available->fields as $field) {
            if ($field->type !== 'image') {
                continue;
            }

            if (!$this->has("field.{$field->id}") || !is_string($this->input("field.{$field->id}"))) {
                continue;
            }

            if (Storage::disk('public')->exists($this->input("field.{$field->id}"))) {
                $this->merge([
                    'field' => [
                        $field->id => new UploadedFile(
                            public_path('storage/') . $this->input("field.{$field->id}"),
                            $this->input("field.{$field->id}"),
                            null,
                            null,
                            true
                        )
                    ] + $this->input('field')
                ]);
            }
        }
    }

    /**
     * [prepareUrl description]
     */
    protected function prepareUrlAttribute() : void
    {
        if ($this->has('url') && $this->input('url') !== null) {
            if ($this->group_available->url === 0) {
                $this->merge(['url' => null]);
            } else {
                $this->merge(['url' => preg_replace('/(\/)$/', null, $this->input('url'))]);
            }
        }
    }

    /**
     * [prepareContentHtml description]
     */
    protected function prepareContentHtmlAttribute() : void
    {
        if ($this->has('content_html')) {
            if ($this->group_available->privileges->contains('name', 'additional options for editing content')) {
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
     * [prepareFieldsRules description]
     * @return array [description]
     */
    protected function prepareFieldsRules() : array
    {
        foreach ($this->group_available->fields as $field) {
            $rules["field.{$field->id}"][] = 'bail';
            $rules["field.{$field->id}"][] = (bool)$field->options->required === true ?
                'required' : 'nullable';

            switch ($field->type) {
                case 'multiselect' :
                case 'checkbox' :
                    $rules["field.{$field->id}"][] = 'array';
                    break;

                case 'image' :
                    $rules["field.{$field->id}"][] = 'image';
                    $rules["field.{$field->id}"][] = 'mimes:jpeg,png,jpg';
                    $rules["field.{$field->id}"][] = 'max:' . $field->options->size;
                    $rules["field.{$field->id}"][] = 'dimensions:max_width=' . $field->options->width . ',max_height=' . $field->options->height;
                    break;

                default :
                    $rules["field.{$field->id}"][] = 'string';
            }

            if (isset($field->options->options)) {
                $rules["field.{$field->id}"][] = 'in:' . implode(',', $field->options->options);
            }
            if (isset($field->options->min)) {
                $rules["field.{$field->id}"][] = 'min:' . $field->options->min;
            }
            if (isset($field->options->max)) {
                $rules["field.{$field->id}"][] = 'max:' . $field->options->max;
            }
        }

        return $rules;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge([
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
                'between:1,' . $this->group_available->max_cats,
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
            'content_html' => 'bail|required|string|no_js_validation',
            'content' => [
                'bail',
                'required',
                'string',
                'between:' . config('idir.dir.min_content') . ',' . config('idir.dir.max_content')
            ],
            'notes' => 'bail|nullable|string|between:3,255',
            'url' => [
                'bail',
                ($this->group_available->url === 2) ? 'required' : 'nullable',
                'string',
                'regex:/^(https|http):\/\/([\da-z\.-]+)(\.[a-z]{2,6})\/?$/',
                'unique:dirs,url'
            ]
        ], $this->prepareFieldsRules());
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'content_html' => 'content'
        ];
    }
}
