<?php

namespace N1ebieski\IDir\Http\ViewComponents\Comment\Dir;

use N1ebieski\ICore\Http\ViewComponents\Comment\LatestComponent as BaseLatestComponent;
use N1ebieski\IDir\Models\Comment\Dir\Comment;
use Illuminate\View\View;

/**
 * [LatestComponent description]
 */
class LatestComponent extends BaseLatestComponent
{
    /**
     * [__construct description]
     * @param Tag  $comment [description]
     * @param int  $limit [description]
     */
    public function __construct(Comment $comment, int $limit = 5)
    {
        parent::__construct($comment, $limit);
    }

    /**
     * [toHtml description]
     * @return View [description]
     */
    public function toHtml() : View
    {
        return view('idir::web.components.comment.dir.latest', [
            'comments' => $this->comment->makeCache()->rememberLatestByComponent([
                'limit' => $this->limit
            ])
        ]);
    }
}
