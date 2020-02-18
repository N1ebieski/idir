<?php

namespace N1ebieski\IDir\Http\ViewComponents\Tag\Dir;

use N1ebieski\ICore\Http\ViewComponents\Tag\TagComponent as BaseTagComponent;
use N1ebieski\IDir\Models\Tag\Dir\Tag;
use Illuminate\View\View;

/**
 * [TagComponent description]
 */
class TagComponent extends BaseTagComponent
{
    /**
     * Undocumented variable
     *
     * @var array|null
     */
    protected $cats;

    /**
     * Undocumented function
     *
     * @param Tag $tag
     * @param integer $limit
     * @param array $cats
     * @param array $colors
     */
    public function __construct(Tag $tag, int $limit = 25, array $colors = null, array $cats = null)
    {
        parent::__construct($tag, $limit, $colors);

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
            ]),
            'colors' => $this->colors
        ]);
    }
}
