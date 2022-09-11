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

namespace N1ebieski\IDir\View\Components;

use Illuminate\Http\Request;
use Illuminate\View\Component;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Link;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection as Collect;
use Illuminate\Contracts\View\Factory as ViewFactory;

class LinkComponent extends Component
{
    /**
     *
     * @var array|null
     */
    protected $cats;

    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param Link $link
     * @param ViewFactory $view
     * @param Collect $collect
     * @param Request $request
     * @param integer $limit
     * @param array $cats
     */
    public function __construct(
        protected Dir $dir,
        protected Link $link,
        protected ViewFactory $view,
        protected Collect $collect,
        protected Request $request,
        protected int $limit = 5,
        array $cats = null
    ) {
        $this->setCats($cats);
    }

    /**
     *
     * @param array|null $cats
     * @return self
     */
    protected function setCats(array $cats = null): self
    {
        $this->cats = !is_null($cats) ?
            $this->collect->make($cats)->flatten()->toArray()
            : null;

        return $this;
    }

    /**
     *
     * @return View
     */
    public function render(): View
    {
        $dirs = !is_null($this->cats) ?
            $this->dir->activeHasLinkPriviligeByComponent([
                'cats' => $this->cats
            ])
            : null;

        $links = $this->link->makeCache()->rememberLinksUnionDirsByComponent($dirs, [
            'home' => $this->request->is('/'),
            'cats' => $this->cats,
            'limit' => $this->limit
        ]);

        return $this->view->make('icore::web.components.link', compact('links'));
    }
}
