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
     * @param integer $max_content
     * @param string $orderby
     */
    public function __construct(
        Comment $comment,
        ViewFactory $view,
        int $limit = 5,
        int $max_content = null,
        string $orderby = 'created_at|desc'
    ) {
        parent::__construct($comment, $view, $limit, $max_content, $orderby);
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
                'max_content' => $this->max_content,
                'orderby' => $this->orderby
            ])
        ]);
    }
}
