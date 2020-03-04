<?php

namespace N1ebieski\IDir\Http\ViewComponents\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\View\View;

/**
 * [CarouselComponent description]
 */
class CarouselComponent implements Htmlable
{
    /**
     * Model
     * @var Dir
     */
    protected $dir;

    /**
     * Undocumented variable
     *
     * @var ViewFactory
     */
    protected $view;

    /**
     * Undocumented variable
     *
     * @var integer
     */
    protected $limit;

    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param ViewFactory $view
     * @param integer $limit
     */
    public function __construct(Dir $dir, ViewFactory $view, int $limit = null)
    {
        $this->dir = $dir;

        $this->view = $view;

        $this->limit = $limit;
    }

    /**
     * [toHtml description]
     * @return View [description]
     */
    public function toHtml() : View
    {
        return $this->view->make('idir::web.components.dir.carousel', [
            'dirs' => $this->dir->makeCache()->rememberAdvertisingPrivilegedByComponent([
                'limit' => $this->limit
            ])
        ]);
    }
}
