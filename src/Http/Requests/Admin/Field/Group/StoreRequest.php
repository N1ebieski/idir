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

namespace N1ebieski\IDir\Http\Requests\Admin\Field\Group;

use Illuminate\Support\Collection as Collect;
use N1ebieski\IDir\Http\Requests\Admin\Field\StoreRequest as BaseStoreRequest;

class StoreRequest extends BaseStoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge([
            'morphs' => [
                'bail',
                'nullable',
                'array',
                'exists:groups,id'
            ]
        ], parent::rules());
    }

    /**
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public function validated($key = null, $default = null)
    {
        if (is_null($key)) {
            return Collect::make(
                $this->safe()->only(['title', 'type', 'visible', 'desc', 'morphs'])
            )
            ->merge([
                'options' => array_merge(
                    $this->safe()->collect()->get(optional($this->safe())->type, []),
                    $this->safe()->only('required')
                )
            ])
            ->toArray();
        }

        return parent::validated($key, $default);
    }
}
