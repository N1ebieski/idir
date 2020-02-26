<?php

namespace N1ebieski\IDir\Http\Controllers\Admin\Comment\Dir;

use Illuminate\View\View;
use N1ebieski\IDir\Models\Dir;
use Illuminate\Http\JsonResponse;
use N1ebieski\IDir\Models\Comment\Dir\Comment;
use N1ebieski\ICore\Filters\Admin\Comment\IndexFilter;
use N1ebieski\ICore\Http\Requests\Admin\Comment\IndexRequest;
use N1ebieski\ICore\Events\Admin\Comment\StoreEvent as CommentStoreEvent;
use N1ebieski\IDir\Http\Requests\Admin\Comment\Dir\StoreRequest;
use N1ebieski\IDir\Http\Requests\Admin\Comment\Dir\CreateRequest;
use N1ebieski\IDir\Http\Controllers\Admin\Comment\Dir\Polymorphic;
use N1ebieski\ICore\Http\Controllers\Admin\Comment\CommentController as BaseCommentController;

/**
 * [CommentController description]
 */
class CommentController extends BaseCommentController implements Polymorphic
{
    /**
     * Display a listing of Comments.
     *
     * @param  Comment       $comment       [description]
     * @param  IndexRequest  $request       [description]
     * @param  IndexFilter   $filter        [description]
     * @return View                         [description]
     */
    public function index(Comment $comment, IndexRequest $request, IndexFilter $filter) : View
    {
        $comments = $comment->makeRepo()->paginateByFilter($filter->all() + [
            'except' => $request->input('except')
        ]);

        return view('icore::admin.comment.index', [
            'model' => $comment,
            'comments' => $comments,
            'filter' => $filter->all(),
            'paginate' => config('database.paginate')
        ]);
    }

    /**
     * Show the form for creating a new Comment for Dir.
     *
     * @param Dir $dir
     * @param CreateRequest $request
     * @return JsonResponse
     */
    public function create(Dir $dir, CreateRequest $request) : JsonResponse
    {
        return response()->json([
            'success' => '',
            'view' => view('icore::admin.comment.create', [
                'model' => $dir,
                'parent_id' => $request->get('parent_id')
            ])->render()
        ]);
    }

    /**
     * [store description]
     * @param  Dir         $dir    [description]
     * @param  Comment      $comment [description]
     * @param  StoreRequest $request [description]
     * @return JsonResponse          [description]
     */
    public function store(Dir $dir, Comment $comment, StoreRequest $request) : JsonResponse
    {
        $comment = $comment->setMorph($dir)->makeService()
            ->create($request->only(['content', 'parent_id']));

        event(new CommentStoreEvent($comment));

        return response()->json([
            'success' => '',
            'view' => view('icore::admin.comment.partials.comment', [
                'comment' => $comment
            ])->render()
        ]);
    }
}
