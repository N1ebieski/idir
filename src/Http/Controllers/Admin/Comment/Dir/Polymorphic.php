<?php

namespace N1ebieski\IDir\Http\Controllers\Admin\Comment\Dir;

use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Comment\Dir\Comment;
use N1ebieski\IDir\Http\Requests\Admin\Comment\Dir\CreateRequest;
use N1ebieski\IDir\Http\Requests\Admin\Comment\Dir\StoreRequest;
use N1ebieski\ICore\Http\Requests\Admin\Comment\IndexRequest;
use Illuminate\Http\JsonResponse;
use N1ebieski\ICore\Filters\Admin\Comment\IndexFilter;
use Illuminate\View\View;

/**
 * [interface description]
 * @var [type]
 */
interface Polymorphic
{
    /**
     * Display a listing of Comments.
     *
     * @param  Comment       $comment       [description]
     * @param  IndexRequest  $request       [description]
     * @param  IndexFilter   $filter        [description]
     * @return View                         [description]
     */
    public function index(Comment $comment, IndexRequest $request, IndexFilter $filter) : View;

    /**
     * Show the form for creating a new Comment for Dir.
     *
     * @param Dir $dir
     * @param CreateRequest $request
     * @return JsonResponse
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
