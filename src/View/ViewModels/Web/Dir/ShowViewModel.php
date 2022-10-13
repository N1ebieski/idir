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
use Spatie\ViewModels\ViewModel;
use N1ebieski\ICore\Filters\Filter;
use N1ebieski\ICore\Utils\MigrationUtil;
use N1ebieski\IDir\Models\Stat\Dir\Stat;
use N1ebieski\ICore\ValueObjects\Stat\Slug;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\IDir\Models\Comment\Dir\Comment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ShowViewModel extends ViewModel
{
    /**
     *
     * @param Dir $dir
     * @param Comment $comment
     * @param Filter $filter
     * @param Request $request
     * @param MigrationUtil $migrationUtil
     * @return void
     */
    public function __construct(
        public Dir $dir,
        protected Comment $comment,
        protected Filter $filter,
        protected Request $request,
        protected MigrationUtil $migrationUtil
    ) {
        //
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public function related(): Collection
    {
        return $this->dir->makeCache()->rememberRelated();
    }

    /**
     * Undocumented function
     *
     * @return LengthAwarePaginator
     */
    public function comments(): LengthAwarePaginator
    {
        return $this->comment->setRelations(['morph' => $this->dir])
            ->makeCache()
            ->rememberRootsByFilter($this->filter->all());
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function filter(): array
    {
        return $this->filter->all();
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function catsAsArray(): array
    {
        return [
            'ancestors' => $this->dir->categories->pluck('ancestors')->flatten()->pluck('id')->toArray(),
            'self' => $this->dir->categories->pluck('id')->toArray()
        ];
    }

    /**
     * Undocumented function
     *
     * @param string $slug
     * @return Stat|null
     */
    protected function statBySlug(string $slug): ?Stat
    {
        return $this->dir->relationLoaded('stats') ?
            $this->dir->stats->firstWhere('slug', $slug)
            : null;
    }

    /**
     * Undocumented function
     *
     * @return float
     */
    public function statCtr(): float
    {
        $click = $this->statBySlug(Slug::CLICK);
        $view = $this->statBySlug($this->getSlug());

        if (!$click || !$view) {
            return (float)0;
        }

        if ($view->pivot->value <= 0) {
            return (float)0;
        }

        return round(($click->pivot->value / $view->pivot->value) * 100, 2);
    }

    /**
     *
     * @return string
     */
    protected function getSlug(): string
    {
        if ($this->migrationUtil->contains('copy_view_to_visit_in_stats_table')) {
            return Slug::VISIT;
        }

        return Slug::VIEW;
    }

    /**
     * Undocumented function
     *
     * @return integer|null
     */
    public function ratingUserValue(): ?int
    {
        return $this->request->user() ?
            optional($this->dir->ratings->where('user_id', $this->request->user()->id)->first())->rating
            : null;
    }
}
