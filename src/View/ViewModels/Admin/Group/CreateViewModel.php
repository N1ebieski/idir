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

namespace N1ebieski\IDir\View\ViewModels\Admin\Group;

use Illuminate\Http\Request;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Price;
use Spatie\ViewModels\ViewModel;
use N1ebieski\IDir\Models\Privilege;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as Collect;

class CreateViewModel extends ViewModel
{
    /**
     * Undocumented function
     *
     * @param Group $group
     * @param Privilege $privilege
     * @param Price $price
     * @param Request $request
     * @param Collect $collect
     */
    public function __construct(
        protected Group $group,
        protected Privilege $privilege,
        protected Price $price,
        protected Request $request,
        protected Collect $collect
    ) {
        //
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public function privileges(): Collection
    {
        return $this->privilege->orderBy('name', 'asc')->get();
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public function groups(): Collection
    {
        return $this->group->makeRepo()->getDoesntHavePricesExceptSelf();
    }

    /**
     * Undocumented function
     *
     * @param string $type
     * @return Collection
     */
    public function pricesSelectionByType(string $type): Collection
    {
        $prices = is_array($this->request->old("prices.{$type}")) ?
            $this->collect->make($this->request->old("prices.{$type}"))
                ->filter(function ($item) {
                    return isset($item['select']) && $item['price'] !== null;
                })
                ->map(function ($item) {
                    if (!is_numeric($item['price'])) {
                        $item['price'] = null;
                    }

                    return $item;
                })
                ->toArray()
            : [];

        return $this->price->hydrate(array_merge($prices, [['type' => $type]]));
    }
}
