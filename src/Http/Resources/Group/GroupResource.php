<?php

namespace N1ebieski\IDir\Http\Resources\Group;

use N1ebieski\IDir\Models\Group;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\Resources\Json\JsonResource;
use N1ebieski\IDir\Http\Resources\Price\PriceResource;
use N1ebieski\IDir\Http\Resources\Privilege\PrivilegeResource;

class GroupResource extends JsonResource
{
    /**
     * Undocumented function
     *
     * @param Group $group
     */
    public function __construct(Group $group)
    {
        parent::__construct($group);
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
            'slug' => $this->slug,
            'name' => $this->name,
            'position' => $this->position,
            'desc' => $this->desc,
            'border' => $this->border,
            'max_cats' => $this->max_cats,
            'max_models' => $this->max_models,
            'max_models_daily' => $this->max_models_daily,
            'visible' => [
                'value' => $this->visible,
                'label' => Lang::get("idir::groups.visible.{$this->visible}")
            ],
            'apply_status' => [
                'value' => $this->apply_status,
                'label' => Lang::get("idir::groups.apply_status.{$this->apply_status}")
            ],
            'url' => [
                'value' => $this->url,
                'label' => Lang::get("idir::groups.url.{$this->url}")
            ],
            'backlink' => [
                'value' => $this->backlink,
                'label' => Lang::get("idir::groups.backlink.{$this->backlink}")
            ],
            'created_at' => $this->created_at,
            'created_at_diff' => $this->created_at_diff,
            'updated_at' => $this->updated_at,
            'updated_at_diff' => $this->updated_at_diff,
            'alt' => $this->when(
                $this->relationLoaded('alt'),
                function () {
                    return App::make(GroupResource::class, ['group' => $this->alt]);
                },
                null
            ),
            $this->mergeWhen(
                $this->relationLoaded('privileges'),
                function () {
                    return [
                        'privileges' => App::make(PrivilegeResource::class)->collection($this->privileges)
                    ];
                }
            ),
            $this->mergeWhen(
                $this->relationLoaded('prices'),
                function () {
                    return [
                        'prices' => App::make(PriceResource::class)->collection($this->prices)
                    ];
                }
            )
        ];
    }
}