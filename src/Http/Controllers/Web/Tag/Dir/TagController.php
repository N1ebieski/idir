<?php

namespace N1ebieski\IDir\Http\Controllers\Web\Tag\Dir;

use N1ebieski\IDir\Http\Requests\Web\Tag\ShowRequest;
use Illuminate\View\View;
use N1ebieski\IDir\Filters\Web\Tag\ShowFilter;
use N1ebieski\ICore\Models\Tag\Tag;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Http\Controllers\Web\Tag\Dir\Polymorphic;

/**
 * [TagController description]
 */
class TagController implements Polymorphic
{
    /**
     * Display a listing of the Dirs for Tag.
     *
     * @param  Tag  $tag  [description]
     * @param  Dir  $dir [description]
     * @param  ShowRequest $request
     * @param  ShowFilter  $filter [description]
     * @return View       [description]
     */
    public function show(Tag $tag, Dir $dir, ShowRequest $request, ShowFilter $filter) : View
    {
        return view('idir::web.tag.dir.show', [
            'tag' => $tag,
            'filter' => $filter->all(),
            'dirs' => $dir->makeCache()->rememberByTagAndFilter(
                $tag,
                $filter->all(),
                $request->get('page') ?? 1
            ),
        ]);
    }
}