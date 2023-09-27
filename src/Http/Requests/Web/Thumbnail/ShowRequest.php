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

namespace N1ebieski\IDir\Http\Requests\Web\Thumbnail;

use Illuminate\Support\Facades\App;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response as HttpResponse;

class ShowRequest extends FormRequest
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
     * Undocumented function
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->prepareUrlAttribute();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function prepareUrlAttribute(): void
    {
        if ($this->has('url') && is_string($this->input('url'))) {
            try {
                $url = App::make('crypt.thumbnail')->decryptString($this->input('url'));
            } catch (\Exception $e) {
                App::abort(HttpResponse::HTTP_FORBIDDEN, $e->getMessage());
            }

            $this->merge(['url' => $url]);
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
            'url' => [
                'bail',
                'required',
                'string',
                'regex:/^(https|http):\/\/([\d\p{Ll}\.-]+)(\.[a-zA-Z\d-]{2,})/u',
            ]
        ];
    }
}
