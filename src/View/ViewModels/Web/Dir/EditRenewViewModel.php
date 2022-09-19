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

namespace N1ebieski\IDir\View\ViewModels\Web\Dir;

use Illuminate\Http\Request;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Price;
use Spatie\ViewModels\ViewModel;
use N1ebieski\IDir\Models\Field\Dir\Field;
use N1ebieski\IDir\ValueObjects\Price\Type;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\IDir\Models\Category\Dir\Category;

class EditRenewViewModel extends ViewModel
{
    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param Request $request
     */
    public function __construct(
        public Dir $dir,
        protected Request $request
    ) {
        //
    }

    /**
     * Undocumented function
     *
     * @return string|null
     */
    public function paymentType(): ?string
    {
        if (!$this->request->old('payment_type') && $this->dir->group->prices->isNotEmpty()) {
            // @phpstan-ignore-next-line
            return $this->dir->group->prices
                ->sortBy(function ($item) {
                    return array_search($item->type, Type::getAvailable());
                })
                ->first()
                ->type;
        }

        return null;
    }

    /**
     * [paymentCodeSmsSelection description]
     *
     * @return  Price|null  [return description]
     */
    public function paymentCodeSmsSelection(): ?Price
    {
        if ($this->request->old('payment_code_sms') && $this->dir->group->prices->isNotEmpty()) {
            return $this->dir->group->prices->where('id', $this->request->old('payment_code_sms'))->first();
        }

        return $this->pricesByType(Type::CODE_SMS)->first();
    }

    /**
     * [paymentCodeTransferSelection description]
     *
     * @return  Price|null  [return description]
     */
    public function paymentCodeTransferSelection(): ?Price
    {
        if ($this->request->old('payment_code_transfer') && $this->dir->group->prices->isNotEmpty()) {
            return $this->dir->group->prices->where('id', $this->request->old('payment_code_transfer'))->first();
        }

        return $this->pricesByType(Type::CODE_TRANSFER)->first();
    }

    /**
     * [pricesByType description]
     *
     * @param   string      $type  [$type description]
     *
     * @return  Collection         [return description]
     */
    public function pricesByType(string $type): Collection
    {
        return $this->dir->group->prices->where('type', $type)->sortBy('price');
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getAttributesAsValues(): array
    {
        return $this->dir->setAttribute(
            'field',
            $this->dir->fields
                ->keyBy('id')
                ->map(function (Field $item) {
                    if ($item->type->isMap()) {
                        /** @var array */
                        $coords = $item->decode_value;

                        return collect($coords)->map(function ($item) {
                            $item = (array)$item;

                            return $item;
                        })->toArray();
                    }

                    return $item->decode_value;
                })
                ->toArray()
        )
        ->getAttributes();
    }
}
