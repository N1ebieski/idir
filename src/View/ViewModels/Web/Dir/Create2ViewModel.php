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

namespace N1ebieski\IDir\View\ViewModels\Web\Dir;

use Illuminate\Http\Request;
use N1ebieski\IDir\Models\Group;
use Spatie\ViewModels\ViewModel;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\IDir\Models\Category\Dir\Category;
use Illuminate\Contracts\Config\Repository as Config;

class Create2ViewModel extends ViewModel
{
    /**
     *
     * @param Group $group
     * @param Category $category
     * @param Config $config
     * @param Request $request
     * @return void
     */
    public function __construct(
        public Group $group,
        protected Category $category,
        protected Config $config,
        protected Request $request
    ) {
        //
    }

    /**
     * [categoriesSelection description]
     *
     * @return  Collection|null  [return description]
     */
    public function categoriesSelection(): ?Collection
    {
        $categories = $this->request->old('categories') ?? $this->request->session()->get('dir.categories');

        if ($categories) {
            return $this->category->makeRepo()->getByIds($categories);
        }

        return null;
    }

    /**
     * [oldContentHtml description]
     *
     * @return  string|null  [return description]
     */
    public function oldContentHtml(): ?string
    {
        $contentHtml = $this->request->old('content_html') ?? $this->request->session()->get('dir.content_html');

        if ($contentHtml) {
            if (!$this->group->hasEditorPrivilege()) {
                return strip_tags($contentHtml);
            }
        }

        return $contentHtml;
    }
}
