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

namespace N1ebieski\IDir\Http\Resources\Field;

use Illuminate\Support\Facades\Lang;
use N1ebieski\IDir\Models\Field\Field;
use N1ebieski\IDir\ValueObjects\Field\Type;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Field
 * @property int|null $depth
 */
class FieldResource extends JsonResource
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
        return [
            'id' => $this->id,
            'position' => $this->position,
            'title' => $this->title,
            'desc' => $this->desc,
            'type' => $this->type->getValue(),
            'visible' => [
                'value' => $this->visible->getValue(),
                'label' => Lang::get("idir::fields.visible.{$this->visible}")
            ],
            $this->mergeWhen(
                $this->depth === null,
                function () {
                    return [
                        'options' => [
                            'required' => [
                                'value' => $this->options->required->getValue(),
                                'label' => Lang::get("idir::fields.required.{$this->options->required}")
                            ],
                            $this->mergeWhen(
                                in_array($this->type->getValue(), [Type::INPUT, Type::TEXTAREA]),
                                function () {
                                    return [
                                        'min' => $this->options->min,
                                        'max' => $this->options->max
                                    ];
                                }
                            ),
                            $this->mergeWhen(
                                in_array($this->type->getValue(), [Type::SELECT, Type::MULTISELECT, Type::CHECKBOX]),
                                function () {
                                    return [
                                        'options' => $this->options->options
                                    ];
                                }
                            ),
                            $this->mergeWhen(
                                in_array($this->type->getValue(), [Type::IMAGE]),
                                function () {
                                    return [
                                        'width' => $this->options->width,
                                        'height' => $this->options->height,
                                        'size' => $this->options->size
                                    ];
                                }
                            )
                        ]
                    ];
                }
            ),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
