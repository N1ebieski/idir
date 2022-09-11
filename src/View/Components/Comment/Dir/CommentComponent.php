<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\IDir\View\Components\Comment\Dir;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use N1ebieski\IDir\Models\Comment\Dir\Comment;
use Illuminate\Contracts\View\Factory as ViewFactory;

class CommentComponent extends Component
{
    /**
     * Undocumented function
     *
     * @param Comment $comment
     * @param ViewFactory $view
     * @param integer $limit
     * @param integer $maxContent
     * @param string $orderby
     */
    public function __construct(
        protected Comment $comment,
        protected ViewFactory $view,
        protected int $limit = 5,
        protected ?int $maxContent = null,
        protected string $orderby = 'created_at|desc'
    ) {
        //
    }

    /**
     * [toHtml description]
     * @return View [description]
     */
    public function render(): View
    {
        return $this->view->make('idir::web.components.comment.dir.comment', [
            'comments' => $this->comment->makeCache()->rememberByComponent([
                'limit' => $this->limit,
                'max_content' => $this->maxContent,
                'orderby' => $this->orderby
            ])
        ]);
    }
}
