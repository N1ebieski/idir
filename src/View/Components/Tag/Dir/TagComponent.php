<?php

namespace N1ebieski\IDir\View\Components\Tag\Dir;

use N1ebieski\ICore\View\Components\Tag\TagComponent as BaseTagComponent;
use N1ebieski\IDir\Models\Tag\Dir\Tag;
use Illuminate\Contracts\View\Factory as ViewFactory;
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
     * @param ViewFactory $view
     * @param integer $limit
     * @param array $colors
     * @param array $cats
     */
    public function __construct(
        Tag $tag,
        ViewFactory $view,
        int $limit = 25,
        array $colors = null,
        array $cats = null
    ) {
        parent::__construct($tag, $view, $limit, $colors);

        $this->cats = $cats;
    }

    /**
     * [toHtml description]
     * @return View [description]
     */
    public function toHtml() : View
    {
        return $this->view->make('idir::web.components.tag.dir.tag', [
            'tags' => $this->tag->makeCache()->rememberPopularByComponent([
                'limit' => $this->limit,
                'cats' => $this->cats
            ]),
            'colors' => $this->colors
        ]);
    }
}
