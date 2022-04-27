<?php

namespace N1ebieski\IDir\View\ViewModels\Admin\Dir;

use Illuminate\Http\Request;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Link;
use N1ebieski\IDir\Models\User;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Price;
use Spatie\ViewModels\ViewModel;
use N1ebieski\IDir\ValueObjects\Price\Type;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\IDir\Models\Category\Dir\Category;

class EditFull3ViewModel extends ViewModel
{
    /**
     * Undocumented variable
     *
     * @var Dir
     */
    public $dir;

    /**
     * [$group description]
     *
     * @var Group
     */
    public $group;

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
     * Undocumented variable
     *
     * @var User
     */
    protected $user;

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
     * Undocumented function
     *
     * @param Dir $dir
     * @param Group $group
     * @param Category $category
     * @param Link $link
     * @param User $user
     * @param Request $request
     */
    public function __construct(
        Dir $dir,
        Group $group,
        Category $category,
        Link $link,
        User $user,
        Request $request
    ) {
        $this->dir = $dir;
        $this->group = $group;
        $this->category = $category;
        $this->link = $link;
        $this->user = $user;

        $this->request = $request;
    }

    /**
     * [categoriesSelection description]
     *
     * @return  Collection  [return description]
     */
    public function categoriesSelection(): Collection
    {
        return $this->categoriesSelection = $this->category->makeRepo()->getByIds(
            $this->request->session()->get("dirId.{$this->dir->id}.categories")
        );
    }

    /**
     * [backlinks description]
     *
     * @return  Collection|null  [return description]
     */
    public function backlinks(): ?Collection
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
     * @return string|null
     */
    public function paymentType(): ?string
    {
        if (!$this->request->old('payment_type') && $this->group->prices->isNotEmpty()) {
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
        $linkId = $this->request->old('backlink', $this->dir->backlink->link_id ?? null);

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
    public function pricesByType(string $type): Collection
    {
        return $this->group->prices->where('type', $type)->sortBy('price');
    }

    /**
     * [userSelection description]
     *
     * @return  User|null  [return description]
     */
    public function userSelection(): ?User
    {
        $userId = $this->request->old('user');

        if ($userId !== null) {
            return $this->user->find($userId);
        }

        return $this->dir->user;
    }
}
