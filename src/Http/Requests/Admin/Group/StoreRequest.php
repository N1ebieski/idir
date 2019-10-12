<?php

namespace N1ebieski\IDir\Http\Requests\Admin\Group;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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

    public function prepareForValidation()
    {
        if ($this->has('prices')) {
            foreach (['transfer', 'auto_sms'] as $type) {
                if ($this->has("prices.{$type}") && is_array($this->input("prices.{$type}"))) {
                    $this->merge([
                        'prices' => [
                            $type => collect($this->input("prices.{$type}"))->filter(function($item) {
                                return isset($item['select']) && $item['price'] !== null;
                            })->values()->toArray()
                        ] + $this->input("prices")
                    ]);
                }
            }
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
            'name' => 'bail|required|string|between:3,255|unique:groups,name',
            'alt_id' => 'bail|integer|exists:groups,id',
            'visible' => 'bail|required|in:0,1',
            'border' => 'bail|nullable|string|max:255',
            'desc' => 'bail|nullable|string|max:500',
            'max_cats' => 'bail|required|integer',
            'max_models' => 'bail|nullable|integer',
            'max_models_daily' => 'bail|nullable|integer',
            'apply_status' => 'bail|required|in:0,1',
            'backlink' => 'bail|required|in:0,1,2',
            'payment' => 'bail|required|in:0,1',            
            'url' => 'bail|required|in:0,1,2',
            'priv' => 'array|no_js_validation',
            'priv.*' => [
                'bail',
                'integer',
                'distinct',
                'exists:privileges,id',
                'no_js_validation'
            ],
            'prices' => 'bail|array|no_js_validation',
            'prices.*.*.price' => 'bail|numeric|between:0,9999.99|no_js_validation',
            'prices.*.*.days' => 'bail|nullable|integer|no_js_validation',
            'prices.*.*.type' => 'bail|in:transfer,auto_sms|no_js_validation'
        ];
    }
}
