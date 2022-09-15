<?php

namespace N1ebieski\IDir\View\ViewModels\Admin\Group;

use Illuminate\Http\Request;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Price;
use Spatie\ViewModels\ViewModel;
use N1ebieski\IDir\Models\Privilege;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as Collect;

class EditViewModel extends ViewModel
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
        public Group $group,
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
        return $this->privilege->makeRepo()->getWithGroup($this->group->id);
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
        if (is_array($this->request->old("prices.{$type}"))) {
            return $this->price->hydrate(array_merge(
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
                    ->toArray(),
                [['type' => $type]]
            ));
        }

        return $this->group->prices->where('type', $type)
            ->sortBy('price')
            ->add($this->price->make(['type' => $type]));
    }
}
