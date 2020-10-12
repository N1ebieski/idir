<?php

namespace N1ebieski\IDir\View\Components\Comment\Dir;

use N1ebieski\ICore\View\Components\Comment\CommentComponent as BaseCommentComponent;
use N1ebieski\IDir\Models\Comment\Dir\Comment;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\View\View;

class CommentComponent extends BaseCommentComponent
{
    /**
     * Undocumented function
     *
     * @param Comment $comment
     * @param ViewFactory $view
     * @param integer $limit
     */
    public function __construct(
        Comment $comment,
        ViewFactory $view,
        int $limit = 5,
        string $orderby = 'created_at|desc'
    ) {
        parent::__construct($comment, $view, $limit, $orderby);
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
                'orderby' => $this->orderby
            ])
        ]);
    }
}
