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

namespace N1ebieski\IDir\View\Components\Dir;

use Illuminate\View\Component;
use N1ebieski\IDir\Models\Dir;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory as ViewFactory;

class CarouselComponent extends Component
{
    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param ViewFactory $view
     * @param integer $limit
     * @param integer $maxContent
     * @param boolean $shuffle
     */
    public function __construct(
        protected Dir $dir,
        protected ViewFactory $view,
        protected ?int $limit = null,
        protected ?int $maxContent = null,
        protected bool $shuffle = false
    ) {
        //
    }

    /**
     * [toHtml description]
     * @return View [description]
     */
    public function render(): View
    {
        return $this->view->make('idir::web.components.dir.carousel', [
            'dirs' => $this->dir->makeCache()
                ->rememberAdvertisingPrivilegedByComponent([
                    'limit' => $this->limit,
                    'max_content' => $this->maxContent
                ])
                ->when($this->shuffle === true, function ($collection) {
                    return $collection->shuffle();
                })
        ]);
    }
}
