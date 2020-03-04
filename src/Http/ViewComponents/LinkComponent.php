<?php

namespace N1ebieski\IDir\Http\ViewComponents;

use Illuminate\View\View;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Link;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection as Collect;
use Illuminate\Contracts\View\Factory as ViewFactory;

/**
 * [CategoryComponent description]
 */
class LinkComponent implements Htmlable
{
    /**
     * Model
     * @var Dir
     */
    protected $dir;

    /**
     * [private description]
     * @var Link
     */
    protected $link;

    /**
     * Undocumented variable
     *
     * @var ViewFactory
     */
    protected $view;

    /**
     * [private description]
     * @var Collect
     */
    protected $collect;

    /**
     * Number of columns
     * @var int
     */
    protected $limit;

    /**
     * [protected description]
     * @var array|null
     */
    protected $cats;

    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param Link $link
     * @param ViewFactory $view
     * @param Collect $collect
     * @param integer $limit
     * @param array $cats
     */
    public function __construct(
        Dir $dir,
        Link $link,
        ViewFactory $view,
        Collect $collect,
        int $limit = 5,
        array $cats = null
    ) {
        $this->dir = $dir;
        $this->link = $link;

        $this->view = $view;
        $this->collect = $collect;

        $this->limit = $limit;
        $this->makeCats($cats);
    }

    /**
     * Undocumented function
     *
     * @param array|null $cats
     * @return array|null
     */
    protected function makeCats(array $cats = null) : ?array
    {
        return $this->cats = ($cats !== null ?
            $this->collect->make($cats)->flatten()->toArray()
            : null);
    }

    /**
     * [toHtml description]
     * @return View [description]
     */
    public function toHtml() : View
    {
        $dirs = $this->cats !== null ?
            $this->dir->activeHasLinkPriviligeByComponent([
                'cats' => $this->cats
            ])
            : null;

        $links = $this->link->makeCache()->rememberLinksUnionDirsByComponent($dirs, [
            'cats' => $this->cats,
            'limit' => $this->limit
        ]);

        return $this->view->make('icore::web.components.link', compact('links'));
    }
}
