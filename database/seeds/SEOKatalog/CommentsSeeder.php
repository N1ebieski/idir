<?php

namespace N1ebieski\IDir\Seeds\SEOKatalog;

use Illuminate\Support\Facades\DB;
use N1ebieski\IDir\Seeds\SEOKatalog\Jobs\CommentsJob;
use N1ebieski\IDir\Seeds\SEOKatalog\SEOKatalogSeeder;

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
            ->chunk(1000, function ($items) {
                CommentsJob::dispatch($items, $this->userLastId)->onQueue('import');
            });
    }
}
