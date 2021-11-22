<?php

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
     * [$category description]
     *
     * @var Category
     */
    protected $category;

    /**
     * [$group description]
     *
     * @var Group
     */
    public $group;

    /**
     * [$config description]
     *
     * @var Config
     */
    protected $config;

    /**
     * [__construct description]
     *
     * @param   Group     $group     [$post description]
     * @param   Category  $category  [$category description]
     * @param   Config    $config    [$config description]
     * @param   Request   $request   [$request description]
     *
     * @return  [type]               [return description]
     */
    public function __construct(
        Group $group,
        Category $category,
        Config $config,
        Request $request
    ) {
        $this->group = $group;
        $this->category = $category;

        $this->config = $config;
        $this->request = $request;
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
            if (!$this->group->privileges->contains('name', 'additional options for editing content')) {
                return strip_tags($contentHtml);
            }
        }

        return $contentHtml;
    }
}
