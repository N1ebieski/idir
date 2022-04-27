<?php

namespace N1ebieski\IDir\Http\Resources\Price;

use N1ebieski\IDir\Models\Price;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Resources\Json\JsonResource;
use N1ebieski\IDir\Http\Resources\Group\GroupResource;

class PriceResource extends JsonResource
{
    /**
     * Undocumented function
     *
     * @param Price $price
     */
    public function __construct(Price $price)
    {
        parent::__construct($price);
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
            'type' => $this->type->getValue(),
            'price' => $this->price,
            'regular_price' => $this->regular_price,
            'discount_price' => $this->discount_price,
            'discount' => $this->discount,
            'qr_as_image' => $this->qr_as_image,
            'days' => $this->days,
            $this->mergeWhen(
                $this->type->isCode(),
                function () {
                    return [
                        'code' => $this->code
                    ];
                }
            ),
            $this->mergeWhen(
                optional($request->user())->can('admin.prices.view'),
                function () {
                    return [
                        'token' => $this->token
                    ];
                }
            ),
            $this->mergeWhen(
                $this->type->isCodeSms(),
                function () {
                    return [
                        'number' => $this->number
                    ];
                }
            ),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            $this->mergeWhen(
                $this->depth === null,
                function () {
                    return [
                        $this->mergeWhen(
                            $this->relationLoaded('group'),
                            function () {
                                return [
                                    'group' => App::make(GroupResource::class, ['group' => $this->group->setAttribute('depth', 1)])
                                ];
                            }
                        )
                    ];
                }
            )
        ];
    }
}
