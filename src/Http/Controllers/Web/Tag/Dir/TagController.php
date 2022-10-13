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

namespace N1ebieski\IDir\Http\Controllers\Web\Tag\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Support\Facades\App;
use N1ebieski\ICore\Models\Tag\Tag;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Filters\Web\Tag\ShowFilter;
use N1ebieski\IDir\Http\Requests\Web\Tag\ShowRequest;
use N1ebieski\IDir\Http\Controllers\Web\Tag\Dir\Polymorphic;
use N1ebieski\IDir\Events\Web\Tag\Dir\ShowEvent as TagShowEvent;

class TagController implements Polymorphic
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
    public function show(Tag $tag, Dir $dir, ShowRequest $request, ShowFilter $filter): HttpResponse
    {
        $dirs = $dir->makeRepo()->paginateByTagAndFilter($tag, $filter->all());

        Event::dispatch(App::make(TagShowEvent::class, ['dirs' => $dirs]));

        return Response::view('idir::web.tag.dir.show', [
            'tag' => $tag,
            'filter' => $filter->all(),
            'dirs' => $dirs
        ]);
    }
}
