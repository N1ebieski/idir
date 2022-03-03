<?php

namespace N1ebieski\IDir\Http\Resources\Payment\Dir;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use N1ebieski\IDir\Models\Payment\Payment;
use Illuminate\Http\Resources\Json\JsonResource;
use N1ebieski\IDir\Http\Resources\Dir\DirResource;
use N1ebieski\IDir\Http\Resources\Price\PriceResource;

class PaymentResource extends JsonResource
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
     * @param Payment $payment
     */
    public function __construct(Payment $payment, int $depth = 0)
    {
        parent::__construct($payment);

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
            'uuid' => $this->uuid,
            'driver' => $this->driver,
            $this->mergeWhen(
                optional($request->user())->can('admin.dirs.view'),
                function () {
                    return [
                        'logs' => $this->logs
                    ];
                }
            ),
            'status' => [
                'value' => $this->status,
                'label' => Lang::get("idir::payments.status.{$this->status}")
            ],
            'created_at' => $this->created_at,
            'created_at_diff' => $this->created_at_diff,
            'updated_at' => $this->updated_at,
            'updated_at_diff' => $this->updated_at_diff,
            $this->mergeWhen(
                $this->relationLoaded('morph') && $this->depth === 0,
                function () {
                    return [
                        'morph' => App::make(DirResource::class, ['dir' => $this->morph])
                    ];
                }
            ),
            $this->mergeWhen(
                $this->relationLoaded('order'),
                function () {
                    return [
                        'order' => App::make(PriceResource::class, ['price' => $this->order])
                    ];
                }
            )
        ];
    }
}
