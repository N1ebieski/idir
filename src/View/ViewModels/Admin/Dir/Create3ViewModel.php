<?php

namespace N1ebieski\IDir\View\ViewModels\Admin\Dir;

use Illuminate\Http\Request;
use N1ebieski\IDir\Models\Link;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Price;
use Spatie\ViewModels\ViewModel;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\IDir\Models\Category\Dir\Category;
use Illuminate\Contracts\Config\Repository as Config;

class Create3ViewModel extends ViewModel
{
    /**
     * [$category description]
     *
     * @var Category
     */
    protected $category;

    /**
     * [$link description]
     *
     * @var Link
     */
    protected $link;

    /**
     * [$group description]
     *
     * @var Group
     */
    public $group;

    /**
     * [$config description]
     *
     * @var Config
     */
    protected $config;

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
     * @param   Config    $config    [$config description]
     * @param   Request   $request   [$request description]
     */
    public function __construct(
        Group $group,
        Category $category,
        Link $link,
        Config $config,
        Request $request
    ) {
        $this->group = $group;
        $this->category = $category;
        $this->link = $link;

        $this->config = $config;
        $this->request = $request;
    }

    /**
     * [categoriesSelection description]
     *
     * @return  Collection  [return description]
     */
    public function categoriesSelection() : Collection
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
    public function backlinks() : ?Collection
    {
        return $this->backlinks = $this->group->backlink > 0 ?
            $this->link->makeRepo()->getAvailableBacklinksByCats(array_merge(
                $this->categoriesSelection->pluck('ancestors')->flatten()->pluck('id')->toArray(),
                $this->categoriesSelection->pluck('id')->toArray()
            )) : null;
    }

    /**
     * Undocumented function
     *
     * @param string $type
     * @return string
     */
    public function driverByType(string $type) : string
    {
        return $this->config->get("idir.payment.{$type}.driver");
    }

    /**
     * Undocumented function
     *
     * @return string|null
     */
    public function paymentType() : ?string
    {
        if (!$this->request->old('payment_type') && $this->group->prices->isNotEmpty()) {
            return $this->group->prices->sortByDesc('type')->first()->type;
        }

        return null;
    }

    /**
     * [paymentCodeSmsSelection description]
     *
     * @return  Price|null  [return description]
     */
    public function paymentCodeSmsSelection() : ?Price
    {
        if ($this->request->old('payment_code_sms') && $this->group->prices->isNotEmpty()) {
            return $this->group->prices->where('id', $this->request->old('payment_code_sms'))->first();
        }

        return $this->pricesByType('code_sms')->first();
    }

    /**
     * [paymentCodeTransferSelection description]
     *
     * @return  Price|null  [return description]
     */
    public function paymentCodeTransferSelection() : ?Price
    {
        if ($this->request->old('payment_code_transfer') && $this->group->prices->isNotEmpty()) {
            return $this->group->prices->where('id', $this->request->old('payment_code_transfer'))->first();
        }

        return $this->pricesByType('code_transfer')->first();
    }

    /**
     * [backlinkSelection description]
     *
     * @return  Link|null  [return description]
     */
    public function backlinkSelection() : ?Link
    {
        $linkId = $this->request->old('backlink');

        if ($linkId !== null) {
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
    public function pricesByType(string $type) : Collection
    {
        return $this->group->prices->where('type', $type)->sortBy('price');
    }
}
