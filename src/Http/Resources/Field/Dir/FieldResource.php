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

namespace N1ebieski\IDir\Http\Resources\Field\Dir;

use N1ebieski\IDir\Models\Field\Dir\Field;
use N1ebieski\IDir\Http\Resources\Field\FieldResource as BaseFieldResource;

/**
 * @mixin Field
 */
class FieldResource extends BaseFieldResource
{
    /**
     * Undocumented function
     *
     * @param Field $field
     */
    public function __construct(Field $field)
    {
        parent::__construct($field);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return array_merge(parent::toArray($request), [
            'value' => $this->decode_value
        ]);
    }
}
