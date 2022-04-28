<?php

namespace N1ebieski\IDir\View\ViewModels\Web\Dir;

use Illuminate\Http\Request;
use N1ebieski\IDir\Models\Dir;
use Spatie\ViewModels\ViewModel;
use N1ebieski\ICore\Filters\Filter;
use N1ebieski\IDir\Models\Stat\Dir\Stat;
use N1ebieski\ICore\ValueObjects\Stat\Slug;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\IDir\Models\Comment\Dir\Comment;
use Illuminate\Pagination\LengthAwarePaginator;

class ShowViewModel extends ViewModel
{
    /**
     * Undocumented variable
     *
     * @var Dir
     */
    public $dir;

    /**
     * Undocumented variable
     *
     * @var Comment
     */
    protected $comment;

    /**
     * Undocumented variable
     *
     * @var Filter
     */
    protected $filter;

    /**
     * Undocumented variable
     *
     * @var Request
     */
    protected $request;

    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param Comment $comment
     * @param Filter $filter
     * @param Request $request
     */
    public function __construct(
        Dir $dir,
        Comment $comment,
        Filter $filter,
        Request $request
    ) {
        $this->dir = $dir;
        $this->comment = $comment;

        $this->filter = $filter;
        $this->request = $request;
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
        $view = $this->statBySlug(Slug::VIEW);

        if (!$click || !$view) {
            return (float)0;
        }

        if ($view->pivot->value <= 0) {
            return (float)0;
        }

        return round(($click->pivot->value / $view->pivot->value) * 100, 2);
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
