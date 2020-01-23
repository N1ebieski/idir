<?php

namespace N1ebieski\IDir\Seeds\SEOKatalog;

use N1ebieski\IDir\Seeds\SEOKatalogSeeder;
use N1ebieski\IDir\Models\Comment\Dir\Comment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CommentsSeeder extends SEOKatalogSeeder
{
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
            ->chunk(1000, function($items) {
                $items->each(function($item) {
                    Comment::create([
                        'model_id' => $item->id_site,
                        'model_type' => 'N1ebieski\IDir\Models\Dir',
                        'user_id' => is_int($item->user) && $item->user > 0 ? 
                            $this->user_last_id + $item->user : null,
                        'content_html' => $item->content,
                        'content' => $item->content,
                        'status' => $item->active,
                        'created_at' => Carbon::createFromTimestamp($item->date),
                        'updated_at' => Carbon::createFromTimestamp($item->date)                        
                    ]);
                });
            });
    }
}
