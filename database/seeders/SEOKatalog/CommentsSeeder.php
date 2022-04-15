<?php

namespace N1ebieski\IDir\Database\Seeders\SEOKatalog;

use Illuminate\Support\Facades\DB;
use N1ebieski\IDir\Database\Seeders\SEOKatalog\Jobs\CommentsJob;
use N1ebieski\IDir\Database\Seeders\SEOKatalog\SEOKatalogSeeder;

class CommentsSeeder extends SEOKatalogSeeder
{
    /**
     * Run the database Seeders.
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
