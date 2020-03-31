<?php

namespace N1ebieski\IDir\Seeds\SEOKatalog;

use N1ebieski\IDir\Seeds\SEOKatalog\SEOKatalogSeeder;
use N1ebieski\IDir\Models\Comment\Dir\Comment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CommentsSeeder extends SEOKatalogSeeder
{
    /**
     * Undocumented function
     *
     * @param string $content
     * @return string
     */
    protected static function makeContentHtml(string $content) : string
    {
        return strip_tags(htmlspecialchars_decode($content));
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::connection('import')
            ->table('comments')
            ->orderBy('id')
            ->chunk(1000, function ($items) {
                $items->each(function ($item) {
                    Comment::create([
                        'model_id' => $item->id_site,
                        'model_type' => 'N1ebieski\IDir\Models\Dir',
                        'user_id' => is_int($item->user) && $item->user > 0 ?
                            $this->user_last_id + $item->user : null,
                        'content_html' => $this->makeContentHtml($item->content),
                        'content' => $this->makeContentHtml($item->content),
                        'status' => $item->active,
                        'created_at' => Carbon::createFromTimestamp($item->date),
                        'updated_at' => Carbon::createFromTimestamp($item->date)
                    ]);
                });
            });
    }
}
