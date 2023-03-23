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
            ->chunk($this->config->get('idir.import.job_limit'), function ($items) {
                $items->map(function ($item) {
                    $item->COMMENT = utf8_encode($item->COMMENT);

                    return $item;
                });

                CommentsJob::dispatch($items, $this->userLastId)->onQueue('import');
            });
    }
}
