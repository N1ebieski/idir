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

namespace N1ebieski\IDir\Database\Seeders\SEOKatalog\Jobs;

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
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

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
    public function __construct(
        protected Collection $items,
        protected int $userLastId
    ) {
        //
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function handle(): void
    {
        $this->items->each(function ($item) {
            if (!$this->verify($item)) {
                return;
            }

            DB::transaction(function () use ($item) {
                $comment = new Comment();

                $comment->content_html = $this->getContentHtml($item->content);
                $comment->content = $this->getContentHtml($item->content);
                $comment->status = $item->active;
                // @phpstan-ignore-next-line
                $comment->created_at = Carbon::createFromTimestamp($item->date);
                // @phpstan-ignore-next-line
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
     *
     * @param mixed $item
     * @return bool
     */
    protected function verify($item): bool
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
    protected function getContentHtml(string $content): string
    {
        return strip_tags(htmlspecialchars_decode($content));
    }
}
