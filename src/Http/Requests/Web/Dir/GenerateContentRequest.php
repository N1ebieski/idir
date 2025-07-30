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

namespace N1ebieski\IDir\Http\Requests\Web\Dir;

use N1ebieski\IDir\Models\Group;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property Group $group
 */
class GenerateContentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->group->url->isActive() && $this->group->hasGenerateContentPrivilege();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'bail|required|string|between:3,' . Config::get('idir.dir.max_title'),
            'url' => [
                'bail',
                'required',
                'string',
                'regex:/^(https|http):\/\/([\d\p{Ll}\.-]+)(\.[a-zA-Z\d-]{2,})\/?$/u'
            ]
        ];
    }
}
