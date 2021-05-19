<?php

namespace N1ebieski\IDir\Seeds\PHPLD;

use Illuminate\Support\Facades\DB;
use N1ebieski\IDir\Seeders\PHPLD\Jobs\DirsJob;
use N1ebieski\IDir\Seeds\PHPLD\PHPLDSeeder;

class DirsSeeder extends PHPLDSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::connection('import')
            ->table('link')
            // Trick to get effect distinct by once field
            ->whereIn('ID', function ($query) {
                $query->selectRaw('MIN(ID)')->from('link')
                    ->groupBy('URL');
            })
            ->whereNotNull(['DESCRIPTION', 'TITLE'])
            ->where([
                ['DESCRIPTION', '<>', ''],
                ['TITLE', '<>', '']
            ])
            ->orderBy('ID', 'desc')
            ->chunk(1000, function ($items) {
                $items->map(function ($item) {
                    $item->TITLE = utf8_encode($item->TITLE);
                    $item->DESCRIPTION = utf8_encode($item->DESCRIPTION);
                    $item->META_DESCRIPTION = utf8_encode($item->META_DESCRIPTION);
                    $item->META_KEYWORDS = utf8_encode($item->META_KEYWORDS);

                    return $item;
                });

                DirsJob::dispatch($items, $this->userLastId, $this->groupLastId, $this->fieldLastId)
                    ->onQueue('import');
            });
    }
}
