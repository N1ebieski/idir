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

namespace N1ebieski\IDir\Database\Seeders\PHPLD;

use Illuminate\Support\Facades\DB;
use N1ebieski\IDir\Database\Seeders\PHPLD\PHPLDSeeder;
use N1ebieski\IDir\Database\Seeders\PHPLD\Jobs\DirsJob;

class DirsSeeder extends PHPLDSeeder
{
    /**
     * Run the database Seeders.
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
            ->chunk($this->config->get('idir.import.job_limit'), function ($items) {
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
