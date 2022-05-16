<?php

namespace N1ebieski\IDir\Http\Requests\Admin\Field;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\IDir\ValueObjects\Field\Type;
use N1ebieski\IDir\ValueObjects\Field\Visible;
use N1ebieski\IDir\ValueObjects\Field\Required;

class StoreRequest extends FormRequest
{
    /**
     * [protected description]
     * @var array
     */
    protected $types = [Type::SELECT, Type::MULTISELECT, Type::CHECKBOX];

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
     * [prepareForValidation description]
     */
    public function prepareForValidation(): void
    {
        $this->prepareOptionsAttribute();
    }

    /**
     * [prepareOptionsAttribute description]
     */
    protected function prepareOptionsAttribute(): void
    {
        foreach ($this->types as $type) {
            if (!$this->has($type . '.options') || $this->input($type . '.options') === null) {
                continue;
            }

            $this->merge([
                $type => [
                    'options' => explode("\r\n", $this->input($type . '.options'))
                ]
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
            'title' => 'bail|required|string|between:3,255|no_js_validation',
            'desc' => 'bail|nullable|string|between:3,5000|no_js_validation',
            'type' => [
                'bail',
                'required',
                'string',
                Rule::in(Type::getAvailable()),
                'no_js_validation'
            ],
            'input.min' => $this->input('type') === Type::INPUT ? [
                'bail',
                'required_if:type,' . Type::INPUT,
                'integer',
                'no_js_validation'
            ] : ['no_js_validation'],
            'input.max' => $this->input('type') === Type::INPUT ? [
                'bail',
                'required_if:type,' . Type::INPUT,
                'integer',
                'no_js_validation'
            ] : ['no_js_validation'],
            'textarea.min' => $this->input('type') === Type::TEXTAREA ? [
                'bail',
                'required_if:type,' . Type::TEXTAREA,
                'integer',
                'no_js_validation'
            ] : ['no_js_validation'],
            'textarea.max' => $this->input('type') === Type::TEXTAREA ? [
                'bail',
                'required_if:type,' . Type::TEXTAREA,
                'integer',
                'no_js_validation'
            ] : ['no_js_validation'],
            'select.options' => $this->input('type') === Type::SELECT ? [
                'bail',
                'required_if:type,' . Type::SELECT,
                'array',
                'no_js_validation'
            ] : ['no_js_validation'],
            'multiselect.options' => $this->input('type') === Type::MULTISELECT ? [
                'bail',
                'required_if:type,' . Type::MULTISELECT,
                'array',
                'no_js_validation'
            ] : ['no_js_validation'],
            'checkbox.options' => $this->input('type') === Type::CHECKBOX ? [
                'bail',
                'required_if:type,' . Type::CHECKBOX,
                'array',
                'no_js_validation'
            ] : ['no_js_validation'],
            'image.width' => $this->input('type') === Type::IMAGE ? [
                'bail',
                'required_if:type,' . Type::IMAGE,
                'integer',
                'no_js_validation'
            ] : ['no_js_validation'],
            'image.height' => $this->input('type') === Type::IMAGE ? [
                'bail',
                'required_if:type,' . Type::IMAGE,
                'integer',
                'no_js_validation'
            ] : ['no_js_validation'],
            'image.size' => $this->input('type') === Type::IMAGE ? [
                'bail',
                'required_if:type,' . Type::IMAGE,
                'integer',
                'no_js_validation'
            ] : ['no_js_validation'],
            'visible' => [
                'bail',
                'required',
                Rule::in([Visible::INACTIVE, Visible::ACTIVE])
            ],
            'required' => [
                'bail',
                'required',
                Rule::in([Required::INACTIVE, Required::ACTIVE])
            ]
        ];
    }
}
