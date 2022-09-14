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

namespace N1ebieski\IDir\Http\Requests\Admin\BanModel\Dir;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\IDir\ValueObjects\BanValue\Type;

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
            'user' => [
                'nullable',
                'integer',
                'required_without_all:ip,url',
                'exists:users,id',
                Rule::unique('bans_models', 'model_id')->where(function ($query) {
                    $query->where('model_type', 'N1ebieski\ICore\Models\User');
                })
            ],
            'ip' => [
                'nullable',
                'string',
                'required_without_all:user,url',
                Rule::unique('bans_values', 'value')->where(function ($query) {
                    $query->where('type', Type::IP);
                })
            ],
            'url' => [
                'nullable',
                'string',
                'required_without_all:user,ip',
                Rule::unique('bans_values', 'value')->where(function ($query) {
                    $query->where('type', Type::URL);
                })
            ]
        ];
    }
}
