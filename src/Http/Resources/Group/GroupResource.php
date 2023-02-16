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

namespace N1ebieski\IDir\Http\Resources\Group;

use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Price;
use Illuminate\Support\Facades\Lang;
use N1ebieski\IDir\Models\Privilege;
use N1ebieski\IDir\Models\Field\Group\Field;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Group
 * @property int|null $depth
 */
class GroupResource extends JsonResource
{
    /**
     *
     * @param Group $group
     * @return void
     */
    public function __construct(Group $group)
    {
        parent::__construct($group);
    }

    /**
     * Transform the resource into an array.
     *
     * @responseField id int
     * @responseField slug string
     * @responseField position int
     * @responseField name string
     * @responseField desc string
     * @responseField border string Class of border.
     * @responseField max_cats int Maximum number of categories to which the entry can be added.
     * @responseField max_models int Maximum number of entries that can be in the group.
     * @responseField max_models_daily int Daily maximum number of entries that can be in the group.
     * @responseField visible int Indicates whether the group is public or not.
     * @responseField apply_status int Entry status after adding.
     * @responseField url int Whether the url is require.
     * @responseField backlink int Whether the backlink is require.
     * @responseField created_at string
     * @responseField updated_at string
     * @responseField alt object Contains relationship alternative Group. Informs to which group the entry will be dropped after expiry of the premium time. If null the entry will be deactivate.
     * @responseField privileges object[] Contains relationship Privileges.
     * @responseField prices object[] Contains relationship Prices.
     * @responseField fields object[] Contains relationship custom Fields.
     * @responseField meta object Paging, filtering and sorting information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug->getValue(),
            'position' => $this->position,
            'name' => $this->name,
            'desc' => $this->desc,
            'border' => $this->border,
            'max_cats' => $this->max_cats,
            'max_models' => $this->max_models,
            'max_models_daily' => $this->max_models_daily,
            'visible' => [
                'value' => $this->visible->getValue(),
                'label' => Lang::get("idir::groups.visible.{$this->visible}")
            ],
            'apply_status' => [
                'value' => $this->apply_status->getValue(),
                'label' => Lang::get("idir::groups.apply_status.{$this->apply_status}")
            ],
            'url' => [
                'value' => $this->url->getValue(),
                'label' => Lang::get("idir::groups.url.{$this->url}")
            ],
            'backlink' => [
                'value' => $this->backlink->getValue(),
                'label' => Lang::get("idir::groups.backlink.{$this->backlink}")
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            $this->mergeWhen(
                $this->depth === null,
                function () {
                    return [
                        'alt' => $this->when(
                            $this->relationLoaded('alt'),
                            function () {
                                return $this->alt?->makeResource();
                            },
                            null
                        ),
                        $this->mergeWhen(
                            $this->relationLoaded('privileges'),
                            function () {
                                /** @var Privilege */
                                $privilege = $this->privileges()->make();

                                return [
                                    'privileges' => $privilege->makeResource()->collection($this->privileges)
                                ];
                            }
                        ),
                        $this->mergeWhen(
                            $this->relationLoaded('prices'),
                            function () {
                                /** @var Price */
                                $price = $this->prices()->make();

                                return [
                                    'prices' => $price->makeResource()->collection($this->prices)
                                ];
                            }
                        ),
                        $this->mergeWhen(
                            $this->relationLoaded('fields'),
                            function () {
                                /** @var Field */
                                $field = $this->fields()->make();

                                return [
                                    'fields' => $field->makeResource()
                                        ->collection($this->fields->map(function (Field $field) {
                                            // @phpstan-ignore-next-line
                                            $field->setAttribute('depth', $field->depth);

                                            return $field;
                                        }))
                                ];
                            }
                        )
                    ];
                }
            ),
        ];
    }
}
