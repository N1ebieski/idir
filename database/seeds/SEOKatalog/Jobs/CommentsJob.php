<?php

namespace N1ebieski\IDir\Seeds\SEOKatalog\Jobs;

use Exception;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use N1ebieski\IDir\Models\Comment\Dir\Comment;

class CommentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Undocumented variable
     *
     * @var Collection
     */
    protected $items;

    /**
     * Undocumented variable
     *
     * @var int
     */
    protected $userLastId;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 10;

    /**
     * Undocumented function
     *
     * @param Collection $items
     * @param integer $userLastId
     */
    public function __construct(Collection $items, int $userLastId)
    {
        $this->items = $items;

        $this->userLastId = $userLastId;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function handle() : void
    {
        $this->items->each(function ($item) {
            if (!$this->verify($item)) {
                return;
            }

            DB::transaction(function () use ($item) {
                $comment = Comment::make();

                $comment->content_html = $this->contentHtml($item->content);
                $comment->content = $this->contentHtml($item->content);
                $comment->status = $item->active;
                $comment->created_at = Carbon::createFromTimestamp($item->date);
                $comment->updated_at = Carbon::createFromTimestamp($item->date);

                $comment->user()->associate(
                    !empty($item->user) && User::find($this->userLastId + $item->user) !== null ?
                        $this->userLastId + $item->user
                        : null
                );
                $comment->morph()->associate(Dir::find($item->id_site));

                $comment->save();
            });
        });
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        //
    }

    /**
     * Undocumented function
     *
     * @param object $item
     * @return boolean
     */
    protected function verify(object $item) : bool
    {
        return Comment::where('id', $item->id)->first() === null
            && !empty($item->id_site)
            && Dir::find($item->id_site) !== null;
    }

    /**
     * Undocumented function
     *
     * @param string $content
     * @return string
     */
    protected static function contentHtml(string $content) : string
    {
        return strip_tags(htmlspecialchars_decode($content));
    }
}
