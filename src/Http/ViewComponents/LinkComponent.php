<?php

namespace N1ebieski\IDir\Http\ViewComponents;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection as Collect;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Link;
use Illuminate\View\View;

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
     * [__construct description]
     * @param Dir     $dir     [description]
     * @param Link    $link    [description]
     * @param Collect $collect [description]
     * @param int     $limit [description]
     * @param array|null $cats [description]
     */
    public function __construct(Dir $dir, Link $link, Collect $collect, int $limit = 5, array $cats = null)
    {
        $this->dir = $dir;
        $this->link = $link;

        $this->collect = $collect;        

        $this->limit = $limit;
        $this->cats = $cats !== null ?
            $this->collect->make($cats)->flatten()->toArray()
            : null;
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
            ]) : null;

        $links = $this->link->makeCache()->rememberLinksUnionDirsByComponent($dirs, [
            'cats' => $this->cats,
            'limit' => $this->limit
        ]);

        return view('icore::web.components.link', compact('links'));
    }
}
