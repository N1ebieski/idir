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

namespace N1ebieski\IDir\Http\Resources\Payment\Dir;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use Illuminate\Http\Resources\Json\JsonResource;
use N1ebieski\IDir\Http\Resources\Dir\DirResource;
use N1ebieski\IDir\Http\Resources\Price\PriceResource;

/**
 * @mixin Payment
 * @property int|null $depth
 */
class PaymentResource extends JsonResource
{
    /**
     * Undocumented function
     *
     * @param Payment $payment
     */
    public function __construct(Payment $payment)
    {
        parent::__construct($payment);
    }

    /**
     * Transform the resource into an array.
     *
     * @responseField uuid string
     * @responseField driver string
     * @responseField logs string (available only for admin.dirs.view).
     * @responseField status object
     * @responseField created_at string
     * @responseField updated_at string
     * @responseField morph object Contains relationship Dir.
     * @responseField order object Contains relationship Price.
     * @responseField url string Link to the driver's payment page (for transfer type payment).
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
                'value' => $this->status->getValue(),
                'label' => Lang::get("idir::payments.status.{$this->status}")
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            $this->mergeWhen(
                $this->depth === null,
                function () {
                    return [
                        $this->mergeWhen(
                            $this->relationLoaded('morph'),
                            function () {
                                return [
                                    'morph' => App::make(DirResource::class, ['dir' => $this->morph->setAttribute('depth', 1)])
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
            ),
        ];
    }
}
