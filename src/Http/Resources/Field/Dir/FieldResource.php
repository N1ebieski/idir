<?php

namespace N1ebieski\IDir\Http\Resources\Field\Dir;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use N1ebieski\IDir\Models\Region\Region;
use N1ebieski\IDir\Models\Field\Dir\Field;
use Illuminate\Http\Resources\Json\JsonResource;
use N1ebieski\IDir\Http\Resources\Dir\DirResource;
use N1ebieski\IDir\Http\Resources\Region\RegionResource;

class FieldResource extends JsonResource
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
        return [
            'id' => $this->id,
            'position' => $this->position,
            'title' => $this->title,
            'desc' => $this->desc,
            'type' => $this->type,
            'visible' => [
                'value' => $this->visible,
                'label' => Lang::get("idir::fields.visible.{$this->visible}")
            ],
            'options' => [
                'required' => [
                    'value' => (int)$this->options->required,
                    'label' => Lang::get("idir::fields.required.{$this->options->required}")
                ],
                $this->mergeWhen(
                    in_array($this->type, ['input', 'textarea']),
                    function () {
                        return [
                            'min' => (int)$this->options->min,
                            'max' => (int)$this->options->max
                        ];
                    }
                ),
                $this->mergeWhen(
                    in_array($this->type, ['regions']),
                    function () {
                        /**
                         * @var Region
                         */
                        $region = Region::make();

                        return [
                            'options' => App::make(RegionResource::class)->collection($region->makeCache()->rememberAll())
                        ];
                    }
                ),
                $this->mergeWhen(
                    in_array($this->type, ['select', 'multiselect', 'checkbox']),
                    function () {
                        return [
                            'options' => $this->options->options
                        ];
                    }
                ),
                $this->mergeWhen(
                    in_array($this->type, ['image']),
                    function () {
                        return [
                            'width' => (int)$this->options->width,
                            'height' => (int)$this->options->height,
                            'size' => (int)$this->options->size
                        ];
                    }
                )
            ],
            'created_at' => $this->created_at,
            'created_at_diff' => $this->created_at_diff,
            'updated_at' => $this->updated_at,
            'updated_at_diff' => $this->updated_at_diff,
            'value' => $this->decode_value,
            $this->mergeWhen(
                $this->relationLoaded('morph') && $this->depth === 0,
                function () {
                    return [
                        'morph' => App::make(DirResource::class, ['dir' => $this->dir])
                    ];
                }
            )
        ];
    }
}