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

namespace N1ebieski\IDir\View\ViewModels\Admin\Dir;

use Illuminate\Http\Request;
use N1ebieski\IDir\Models\Link;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Price;
use Spatie\ViewModels\ViewModel;
use N1ebieski\IDir\ValueObjects\Price\Type;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\IDir\Models\Category\Dir\Category;

class Create3ViewModel extends ViewModel
{
    /**
     * [$categoriesSelection description]
     *
     * @var Collection
     */
    public $categoriesSelection;

    /**
     * [$backlinks description]
     *
     * @var Collection|null
     */
    public $backlinks;

    /**
     * [__construct description]
     *
     * @param   Group     $group     [$group description]
     * @param   Category  $category  [$category description]
     * @param   Link      $link      [$link description]
     * @param   Request   $request   [$request description]
     */
    public function __construct(
        public Group $group,
        protected Category $category,
        protected Link $link,
        protected Request $request
    ) {
        //
    }

    /**
     * [categoriesSelection description]
     *
     * @return  Collection  [return description]
     */
    public function categoriesSelection(): Collection
    {
        return $this->categoriesSelection = $this->category->makeRepo()->getByIds(
            $this->request->session()->get('dir.categories')
        );
    }

    /**
     * [backlinks description]
     *
     * @return  Collection|null  [return description]
     */
    public function backlinks(): ?Collection
    {
        return $this->backlinks = !$this->group->backlink->isInactive() ?
            $this->link->makeRepo()->getAvailableBacklinksByCats(array_merge(
                $this->categoriesSelection->pluck('ancestors')->flatten()->pluck('id')->toArray(),
                $this->categoriesSelection->pluck('id')->toArray()
            )) : null;
    }

    /**
     * Undocumented function
     *
     * @return string|null
     */
    public function paymentType(): ?string
    {
        if (!$this->request->old('payment_type') && $this->group->prices->isNotEmpty()) {
            // @phpstan-ignore-next-line
            return $this->group->prices
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
        if ($this->request->old('payment_code_sms') && $this->group->prices->isNotEmpty()) {
            return $this->group->prices->where('id', $this->request->old('payment_code_sms'))->first();
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
        if ($this->request->old('payment_code_transfer') && $this->group->prices->isNotEmpty()) {
            return $this->group->prices->where('id', $this->request->old('payment_code_transfer'))->first();
        }

        return $this->pricesByType(Type::CODE_TRANSFER)->first();
    }

    /**
     * [backlinkSelection description]
     *
     * @return  Link|null  [return description]
     */
    public function backlinkSelection(): ?Link
    {
        $linkId = $this->request->old('backlink');

        if ($linkId !== null) {
            /** @var Link|null */
            return $this->link->find($linkId);
        }

        return optional($this->backlinks)->first();
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
        return $this->group->prices->where('type', $type)->sortBy('price');
    }
}
