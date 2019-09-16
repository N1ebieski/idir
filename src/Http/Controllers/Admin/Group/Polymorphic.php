<?php

namespace N1ebieski\ICore\Http\Controllers\Admin\Category;

use N1ebieski\ICore\Models\Category\Category;
use N1ebieski\ICore\Http\Requests\Admin\Category\IndexRequest;
use N1ebieski\ICore\Http\Requests\Admin\Category\StoreRequest;
use N1ebieski\ICore\Http\Requests\Admin\Category\StoreGlobalRequest;
use N1ebieski\ICore\Http\Requests\Admin\Category\SearchRequest;
use N1ebieski\ICore\Filters\Admin\Category\IndexFilter;

interface Polymorphic
{
    public function index(Category $category, IndexRequest $request, IndexFilter $filter);

    public function create(Category $category);

    public function store(Category $category, StoreRequest $request);

    public function storeGlobal(Category $category, StoreGlobalRequest $request);

    public function search(Category $category, SearchRequest $request);
}
