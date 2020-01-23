<?php

namespace N1ebieski\IDir\Http\ViewComponents\Tag\Dir;

use Illuminate\Contracts\Support\Htmlable;
use N1ebieski\IDir\Models\Tag\Dir\Tag;
use Illuminate\View\View;

/**
 * [TagComponent description]
 */
class TagComponent implements Htmlable
{
    /**
     * [private description]
     * @var Tag
     */
    protected $tag;

    /**
     * Undocumented variable
     *
     * @var int
     */
    protected $limit;

    /**
     * Undocumented variable
     *
     * @var array|null
     */
    protected $cats;

    /**
     * [__construct description]
     * @param Tag   $tag  [description]
     * @param array $cats [description]
     */
    public function __construct(Tag $tag, int $limit = 25, array $cats = null)
    {
        $this->tag = $tag;

        $this->limit = $limit;        
        $this->cats = $cats;
    }

    /**
     * [toHtml description]
     * @return View [description]
     */
    public function toHtml() : View
    {
        return view('idir::web.components.tag.dir.tag', [
            'tags' => $this->tag->makeCache()->rememberPopularByComponent([
                'limit' => $this->limit,
                'cats' => $this->cats
            ])
        ]);
    }
}
