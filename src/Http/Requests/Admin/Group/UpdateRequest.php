<?php

namespace N1ebieski\IDir\Http\Requests\Admin\Group;

use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\IDir\Http\Requests\Admin\Group\Traits\CodePayable;
use Illuminate\Support\Collection as Collect;

class UpdateRequest extends FormRequest
{
    use CodePayable;

    /**
     * [protected description]
     * @var array
     */
    protected $types = ['transfer', 'code_sms', 'code_transfer'];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->group->isNotDefault();
    }

    /**
     * [prepareForValidation description]
     */
    public function prepareForValidation() : void
    {
        $this->preparePricesAttribute();
    }

    /**
     * [preparePricesAttribute description]
     */
    protected function preparePricesAttribute() : void
    {
        if (!$this->has('prices')) {
            return;
        }

        foreach ($this->types as $type) {
            if (!$this->has("prices.{$type}") || !is_array($this->input("prices.{$type}"))) {
                continue;
            }

            $this->merge([
                'prices' => [
                    $type => Collect::make($this->input("prices.{$type}"))->filter(function ($item) {
                        return isset($item['select']) && $item['price'] !== null;
                    })->map(function ($item) {
                        if (isset($item['codes']['codes']) && is_string($item['codes']['codes'])) {
                            $item['codes']['codes'] = $this->prepareCodes($item['codes']['codes']);
                        }
                        return $item;
                    })->values()->toArray()
                ] + $this->input("prices")
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
            'name' => 'bail|required|string|between:3,255|unique:groups,name,' . $this->group->id,
            'alt_id' => 'bail|nullable|integer|exists:groups,id',
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
                'nullable',
                'integer',
                'distinct',
                'exists:privileges,id',
                'no_js_validation'
            ],
            'prices' => 'bail|array|no_js_validation',
            'prices.*.*.id' => 'bail|nullable|integer|exists:prices,id|no_js_validation',
            'prices.*.*.price' => 'bail|numeric|between:0,9999.99|no_js_validation',
            'prices.*.*.days' => 'bail|nullable|integer|no_js_validation',
            'prices.*.*.type' => 'bail|in:transfer,code_sms,code_transfer|no_js_validation',
            'prices.*.*.code' => 'bail|nullable|string|no_js_validation',
            'prices.*.*.token' => 'bail|nullable|string|no_js_validation',
            'prices.*.*.number' => 'bail|nullable|integer|no_js_validation',
            'prices.*.*.codes.codes' => 'bail|nullable|array|no_js_validation',
        ];
    }
}
