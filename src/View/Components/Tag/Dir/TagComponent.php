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

namespace N1ebieski\IDir\View\Components\Tag\Dir;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use N1ebieski\IDir\Models\Tag\Dir\Tag;
use Illuminate\Contracts\View\Factory as ViewFactory;

class TagComponent extends Component
{
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
        protected Tag $tag,
        protected ViewFactory $view,
        protected int $limit = 25,
        protected ?array $colors = null,
        protected ?array $cats = null
    ) {
        //
    }

    /**
     *
     * @return View
     */
    public function render(): View
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
