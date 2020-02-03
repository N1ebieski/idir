<?php

namespace N1ebieski\IDir\Http\Controllers\Web\Comment\Dir;

use N1ebieski\IDir\Http\Requests\Web\Comment\Dir\CreateRequest;
use N1ebieski\IDir\Http\Requests\Web\Comment\Dir\StoreRequest;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Comment\Dir\Comment;
use Illuminate\Http\JsonResponse;

/**
 * [interface description]
 * @var [type]
 */
interface Polymorphic
{
    /**
     * Show the form for creating a new Comment for Dir.
     *
     * @param  Dir          $dir    [description]
     * @param  CreateRequest $request [description]
     * @return JsonResponse           [description]
     */
    public function create(Dir $dir, CreateRequest $request) : JsonResponse;

    /**
     * [store description]
     * @param  Dir         $dir    [description]
     * @param  Comment      $comment [description]
     * @param  StoreRequest $request [description]
     * @return JsonResponse          [description]
     */
    public function store(Dir $dir, Comment $comment, StoreRequest $request) : JsonResponse;
}
