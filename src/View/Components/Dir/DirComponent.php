<?php

namespace N1ebieski\IDir\View\Components\Dir;

use Illuminate\View\View;
use N1ebieski\IDir\Models\Dir;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\Factory as ViewFactory;

class DirComponent implements Htmlable
{
    /**
     * Undocumented variable
     *
     * @var ViewFactory
     */
    protected $view;

    /**
     * Undocumented variable
     *
     * @var Dir
     */
    protected $dir;

    /**
     * Undocumented variable
     *
     * @var int
     */
    protected $limit;

    /**
     * Undocumented variable
     *
     * @var int
     */
    protected $cols;

    /**
     * Undocumented variable
     *
     * @var [type]
     */
    protected $max_content;

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $orderby;

    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param ViewFactory $view
     * @param integer $limit
     * @param integer $cols
     * @param string $orderby
     */
    public function __construct(
        Dir $dir,
        ViewFactory $view,
        int $limit = 4,
        int $cols = 4,
        int $max_content = 100,
        string $orderby = 'created_at|desc'
    ) {
        $this->dir = $dir;

        $this->view = $view;

        $this->limit = $limit;
        $this->cols = $cols;
        $this->max_content = $max_content;
        $this->orderby = $orderby;
    }

    /**
     * [toHtml description]
     * @return View [description]
     */
    public function toHtml() : View
    {
        return $this->view->make('idir::web.components.dir.dir', [
            'dirs' => $this->dir->makeCache()->rememberByComponent([
                'limit' => $this->limit,
                'max_content' => $this->max_content,
                'orderby' => $this->orderby
            ]),
            'cols' => $this->cols
        ]);
    }
}
