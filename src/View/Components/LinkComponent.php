<?php

namespace N1ebieski\IDir\View\Components;

use Illuminate\View\View;
use Illuminate\Http\Request;
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
     * Undocumented variable
     *
     * @var Request
     */
    protected $request;

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
     * @param Request $request
     * @param integer $limit
     * @param array $cats
     */
    public function __construct(
        Dir $dir,
        Link $link,
        ViewFactory $view,
        Collect $collect,
        Request $request,
        int $limit = 5,
        array $cats = null
    ) {
        $this->dir = $dir;
        $this->link = $link;

        $this->view = $view;
        $this->collect = $collect;
        $this->request = $request;

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
            'home' => $this->request->is('/'),
            'cats' => $this->cats,
            'limit' => $this->limit
        ]);

        return $this->view->make('icore::web.components.link', compact('links'));
    }
}
