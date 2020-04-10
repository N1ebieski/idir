<?php

namespace N1ebieski\IDir\Crons\Sitemap\Builder\Category\Dir;

use Closure;
use Illuminate\Support\Carbon;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Support\Collection as Collect;
use N1ebieski\ICore\Crons\Sitemap\Builder\Builder;
use N1ebieski\IDir\Models\Category\Dir\Category;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Routing\UrlGenerator as URL;
use Illuminate\Contracts\Filesystem\Factory as Storage;

class CategoryBuilder extends Builder
{
    /**
     * Undocumented variable
     *
     * @var Category
     */
    protected $category;

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $route = 'web.category.dir.show';

    /**
     * [protected description]
     * @var string
     */
    protected $path = 'vendor/idir/sitemap/categories/dirs';

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $priority = '0.8';

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $changefreq = 'weekly';

    /**
     * Undocumented function
     *
     * @param ArrayToXml $arrayToXml
     * @param URL $url
     * @param Carbon $carbon
     * @param Storage $storage
     * @param Config $config
     * @param Collect $collect
     * @param Page $page
     */
    public function __construct(
        ArrayToXml $arrayToXml,
        URL $url,
        Carbon $carbon,
        Storage $storage,
        Config $config,
        Collect $collect,
        Category $category
    ) {
        parent::__construct($arrayToXml, $url, $carbon, $storage, $config, $collect);

        $this->category = $category;
    }

    /**
     * Undocumented function
     *
     * @param Closure $callback
     * @return void
     */
    public function chunkCollection(Closure $callback) : bool
    {
        return $this->category->makeRepo()->chunkActiveWithModelsCount($callback);
    }
}
