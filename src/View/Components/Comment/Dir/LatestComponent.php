<?php

namespace N1ebieski\IDir\View\Components\Comment\Dir;

use N1ebieski\ICore\View\Components\Comment\LatestComponent as BaseLatestComponent;
use N1ebieski\IDir\Models\Comment\Dir\Comment;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\View\View;

/**
 * [LatestComponent description]
 */
class LatestComponent extends BaseLatestComponent
{
    /**
     * Undocumented function
     *
     * @param Comment $comment
     * @param ViewFactory $view
     * @param integer $limit
     * @param integer $max_content
     */
    public function __construct(
        Comment $comment,
        ViewFactory $view,
        int $limit = 5,
        int $max_content = null
    ) {
        parent::__construct($comment, $view, $limit, $max_content);
    }

    /**
     * [toHtml description]
     * @return View [description]
     */
    public function toHtml() : View
    {
        return $this->view->make('idir::web.components.comment.dir.comment', [
            'comments' => $this->comment->makeCache()->rememberByComponent([
                'limit' => $this->limit,
                'orderby' => 'created_at|desc'
            ])
        ]);
    }
}
