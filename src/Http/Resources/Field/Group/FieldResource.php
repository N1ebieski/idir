<?php

namespace N1ebieski\IDir\Http\Resources\Field\Group;

use Illuminate\Support\Facades\App;
use N1ebieski\IDir\Models\Field\Group\Field;
use N1ebieski\IDir\Http\Resources\Group\GroupResource;
use N1ebieski\IDir\Http\Resources\Field\FieldResource as BaseFieldResource;

class FieldResource extends BaseFieldResource
{
    /**
     * Undocumented variable
     *
     * @var int
     */
    protected $depth;

    /**
     * Undocumented function
     *
     * @param Field $field
     * @param integer $depth
     */
    public function __construct(Field $field, int $depth = 0)
    {
        parent::__construct($field);

        $this->depth = $depth;
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
            $this->mergeWhen(
                $this->relationLoaded('morphs') && $this->depth === 0,
                function () {
                    return [
                        'morphs' => App::make(GroupResource::class)->collection($this->morphs)
                    ];
                }
            )
        ]);
    }
}
