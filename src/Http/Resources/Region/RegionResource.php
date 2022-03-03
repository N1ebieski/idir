<?php

namespace N1ebieski\IDir\Http\Resources\Region;

use N1ebieski\IDir\Models\Region\Region;
use Illuminate\Http\Resources\Json\JsonResource;

class RegionResource extends JsonResource
{
    /**
     * Undocumented function
     *
     * @param Region $group
     */
    public function __construct(Region $region)
    {
        parent::__construct($region);
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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
