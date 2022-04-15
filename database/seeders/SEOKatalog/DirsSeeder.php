<?php

namespace N1ebieski\IDir\Database\Seeders\SEOKatalog;

use Illuminate\Support\Facades\DB;
use N1ebieski\IDir\Database\Seeders\SEOKatalog\Jobs\DirsJob;
use N1ebieski\IDir\Database\Seeders\SEOKatalog\SEOKatalogSeeder;

class DirsSeeder extends SEOKatalogSeeder
{
    /**
     * Run the database Seeders.
     *
     * @return void
     */
    public function run()
    {
        DB::connection('import')
            ->table('sites')
            // Trick to get effect distinct by once field
            ->whereIn('id', function ($query) {
                $query->selectRaw('MIN(id)')->from('sites')
                    ->groupBy('url');
            })
            ->orderBy('id', 'desc')
            ->chunk(1000, function ($items) {
                DirsJob::dispatch($items, $this->userLastId, $this->groupLastId, $this->fieldLastId)
                    ->onQueue('import');
            });
    }
}
