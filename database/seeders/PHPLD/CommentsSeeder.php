<?php

namespace N1ebieski\IDir\Database\Seeders\PHPLD;

use Illuminate\Support\Facades\DB;
use N1ebieski\IDir\Database\Seeders\PHPLD\PHPLDSeeder;
use N1ebieski\IDir\Database\Seeders\PHPLD\Jobs\CommentsJob;

class CommentsSeeder extends PHPLDSeeder
{
    /**
     * Run the database Seeders.
     *
     * @return void
     */
    public function run()
    {
        DB::connection('import')
            ->table('comment')
            ->orderBy('ID')
            ->chunk(1000, function ($items) {
                $items->map(function ($item) {
                    $item->COMMENT = utf8_encode($item->COMMENT);

                    return $item;
                });

                CommentsJob::dispatch($items, $this->userLastId)->onQueue('import');
            });
    }
}
