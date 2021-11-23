<?php

namespace N1ebieski\IDir\View\ViewModels\Web\Dir;

use Illuminate\Http\Request;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Group;
use Spatie\ViewModels\ViewModel;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\IDir\Models\Category\Dir\Category;
use Illuminate\Contracts\Config\Repository as Config;

class Edit2ViewModel extends ViewModel
{
    /**
     * [$dir description]
     *
     * @var Dir
     */
    public $dir;

    /**
     * [$group description]
     *
     * @var Group
     */
    public $group;

    /**
     * [$category description]
     *
     * @var Category
     */
    protected $category;

    /**
     * [$config description]
     *
     * @var Config
     */
    protected $config;

    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param Group $group
     * @param Category $category
     * @param Config $config
     * @param Request $request
     */
    public function __construct(
        Dir $dir,
        Group $group,
        Category $category,
        Config $config,
        Request $request
    ) {
        $this->dir = $dir;
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
        $categories = $this->request->old('categories') ??
            $this->request->session()->get("dirId.{$this->dir->id}.categories");

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
        $contentHtml = $this->request->old('content_html') ??
            $this->request->session()->get("dirId.{$this->dir->id}.content_html");

        if ($contentHtml) {
            if (!$this->group->privileges->contains('name', 'additional options for editing content')) {
                return strip_tags($contentHtml);
            }
        }

        return $contentHtml;
    }
}
