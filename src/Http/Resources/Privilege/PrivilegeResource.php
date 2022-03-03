<?php

namespace N1ebieski\IDir\Http\Resources\Privilege;

use N1ebieski\IDir\Models\Privilege;
use Illuminate\Http\Resources\Json\JsonResource;

class PrivilegeResource extends JsonResource
{
    /**
     * Undocumented function
     *
     * @param Privilege $privilege
     */
    public function __construct(Privilege $privilege)
    {
        parent::__construct($privilege);
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
            'name' => __($this->name),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
