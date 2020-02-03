<?php

namespace N1ebieski\IDir\Http\Requests\Admin\Field;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * [protected description]
     * @var array
     */
    protected $types = ['select', 'multiselect', 'checkbox'];

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
    public function prepareForValidation() : void
    {
        $this->prepareOptionsAttribute();
    }

    /**
     * [prepareOptionsAttribute description]
     */
    protected function prepareOptionsAttribute() : void
    {
        foreach ($this->types as $type) {
            if (!$this->has($type.'.options') || $this->input($type.'.options') === null) {
                continue;
            }

            $this->merge([
                $type => [
                    'options' => explode("\r\n", $this->input($type.'.options'))
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
            'type' => $this->field->isNotDefault() ?
                'bail|required|string|in:input,textarea,select,multiselect,checkbox,image|no_js_validation'
                : 'not_present',
            'input.min' => $this->input('type') === 'input' ? [
                'bail',
                'required_if:type,input',
                'integer',
                'no_js_validation'
            ] : ['no_js_validation'],
            'input.max' => $this->input('type') === 'input' ? [
                'bail',
                'required_if:type,input',
                'integer',
                'no_js_validation'
            ] : ['no_js_validation'],
            'textarea.min' => $this->input('type') === 'textarea' ? [
                'bail',
                'required_if:type,textarea',
                'integer',
                'no_js_validation'
            ] : ['no_js_validation'],
            'textarea.max' => $this->input('type') === 'textarea' ? [
                'bail',
                'required_if:type,textarea',
                'integer',
                'no_js_validation'
            ] : ['no_js_validation'],
            'select.options' => $this->input('type') === 'select' ? [
                'bail',
                'required_if:type,select',
                'array',
                'no_js_validation'
            ] : ['no_js_validation'],
            'multiselect.options' => $this->input('type') === 'multiselect' ? [
                'bail',
                'required_if:type,multiselect',
                'array',
                'no_js_validation'
            ] : ['no_js_validation'],
            'checkbox.options' => $this->input('type') === 'checkbox' ? [
                'bail',
                'required_if:type,checkbox',
                'array',
                'no_js_validation'
            ] : ['no_js_validation'],
            'image.width' => $this->input('type') === 'image' ? [
                'bail',
                'required_if:type,image',
                'integer',
                'no_js_validation'
            ] : ['no_js_validation'],
            'image.height' => $this->input('type') === 'image' ? [
                'bail',
                'required_if:type,image',
                'integer',
                'no_js_validation'
            ] : ['no_js_validation'],
            'image.size' => $this->input('type') === 'image' ? [
                'bail',
                'required_if:type,image',
                'integer',
                'no_js_validation'
            ] : ['no_js_validation'],
            'visible' => 'bail|required|in:0,1',
            'required' => 'bail|required|in:0,1'
        ];
    }
}
