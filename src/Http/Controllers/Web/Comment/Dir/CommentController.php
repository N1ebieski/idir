<?php

namespace N1ebieski\IDir\Http\Controllers\Web\Comment\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Response;
use N1ebieski\IDir\Models\Comment\Dir\Comment;
use N1ebieski\IDir\Http\Requests\Web\Comment\Dir\StoreRequest;
use N1ebieski\IDir\Http\Requests\Web\Comment\Dir\CreateRequest;
use N1ebieski\ICore\Events\Web\Comment\StoreEvent as CommentStoreEvent;
use N1ebieski\IDir\Http\Controllers\Web\Comment\Dir\Polymorphic as Polymorphic;

/**
 * [CommentController description]
 */
class CommentController implements Polymorphic
{
    /**
     * Show the form for creating a new Comment for Dir.
     *
     * @param  Dir          $dir    [description]
     * @param  CreateRequest $request [description]
     * @return JsonResponse           [description]
     */
    public function create(Dir $dir, CreateRequest $request) : JsonResponse
    {
        return Response::json([
            'success' => '',
            'view' => View::make('icore::web.comment.create', [
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

        Event::dispatch(App::make(CommentStoreEvent::class, ['comment' => $comment]));

        return Response::json([
            'success' => $comment->status === Comment::ACTIVE ?
                null : Lang::get('icore::comments.success.store_0'),
            'view' => $comment->status === Comment::ACTIVE ?
                View::make('icore::web.comment.partials.comment', [
                    'comment' => $comment
                ])->render() : null
        ]);
    }
}
