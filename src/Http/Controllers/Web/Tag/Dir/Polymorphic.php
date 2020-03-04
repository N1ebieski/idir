<?php

namespace N1ebieski\IDir\Http\Controllers\Web\Tag\Dir;

use N1ebieski\IDir\Models\Dir;
use N1ebieski\ICore\Models\Tag\Tag;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Filters\Web\Tag\ShowFilter;
use N1ebieski\IDir\Http\Requests\Web\Tag\ShowRequest;

/**
 * [interface description]
 */
interface Polymorphic
{
    /**
     * Display a listing of the Dirs for Tag.
     *
     * @param  Tag  $tag  [description]
     * @param  Dir  $dir [description]
     * @param  ShowRequest $request
     * @param  ShowFilter  $filter [description]
     * @return HttpResponse       [description]
     */
    public function show(Tag $tag, Dir $dir, ShowRequest $request, ShowFilter $filter) : HttpResponse;
}
