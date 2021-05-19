<?php

namespace N1ebieski\IDir\Seeds\PHPLD\Jobs;

use Exception;
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

                $comment->content_html = $this->contentHtml($item->COMMENT);
                $comment->content = $this->contentHtml($item->COMMENT);
                $comment->status = $item->STATUS === 2 ?
                    Comment::ACTIVE
                    : Comment::INACTIVE;
                $comment->created_at = $item->DATE_ADDED;
                $comment->updated_at = $item->DATE_ADDED;

                $comment->user()->associate(
                    !empty($item->OWNER_ID) && User::find($this->userLastId + $item->OWNER_ID) !== null ?
                        $this->userLastId + $item->OWNER_ID
                        : null
                );
                $comment->morph()->associate(Dir::find($item->ITEM_ID));

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
        return Comment::where('id', $item->ID)->first() === null
            && !empty($item->ITEM_ID)
            && Dir::find($item->ITEM_ID) !== null;
    }

    /**
     * Undocumented function
     *
     * @param string $content
     * @return string
     */
    protected static function contentHtml(string $content) : string
    {
        return strip_tags(htmlspecialchars_decode(utf8_decode($content)));
    }
}
