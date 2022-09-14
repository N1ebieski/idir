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

namespace N1ebieski\IDir\Http\Requests\Admin\Group;

use Illuminate\Validation\Rule;
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'bail|required|string|between:3,255|unique:groups,name',
            'alt_id' => [
                'bail',
                'nullable',
                'integer',
                Rule::exists('groups', 'id')->where(function ($query) {
                    $query->whereNotExists(function ($query) {
                        $query->from('prices')
                            ->whereRaw('`groups`.`id` = `prices`.`group_id`');
                    });
                })
            ],
            'visible' => 'bail|required|in:0,1',
            'border' => 'bail|nullable|string|max:255',
            'desc' => 'bail|nullable|string|max:500',
            'max_cats' => 'bail|required|integer',
            'max_models' => 'bail|nullable|integer',
            'max_models_daily' => 'bail|nullable|integer',
            'apply_status' => 'bail|required|in:0,1',
            'backlink' => 'bail|required|in:0,1,2',
            'url' => 'bail|required|in:0,1,2',
            'priv' => 'array|no_js_validation',
            'priv.*' => [
                'bail',
                'integer',
                'distinct',
                'exists:privileges,id',
                'no_js_validation'
            ]
        ];
    }
}
