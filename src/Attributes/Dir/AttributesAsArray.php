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

namespace N1ebieski\IDir\Attributes\Dir;

use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Field\Dir\Field;
use Illuminate\Support\Collection as Collect;
use Illuminate\Database\Eloquent\Casts\Attribute;

class AttributesAsArray
{
    /**
     *
     * @param Dir $dir
     * @return void
     */
    public function __construct(protected Dir $dir)
    {
        //
    }

    /**
     *
     * @return Attribute
     */
    public function __invoke(): Attribute
    {
        return new Attribute(
            get: function (): array {
                return $this->dir->attributesToArray()
                    + ['field' => $this->dir->fields->keyBy('id')
                        ->map(function (Field $field) {
                            if ($field->type->isMap()) {
                                /** @var array */
                                $coords = $field->decode_value;

                                return Collect::make($coords)->map(function ($item) {
                                    $item = (array)$item;

                                    return $item;
                                })->toArray();
                            }

                            return $field->decode_value;
                        })
                        ->toArray()]
                    + ['categories' => $this->dir->categories->pluck('id')->toArray()]
                    + ['tags' => $this->dir->tags->pluck('name')->toArray()];
            }
        );
    }
}
