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

namespace N1ebieski\IDir\Database\Seeders\SEOKatalog;

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\IDir\Database\Seeders\SEOKatalog\Jobs\CommentsJob;
use N1ebieski\IDir\Database\Seeders\SEOKatalog\SEOKatalogSeeder;

class CommentsSeeder extends SEOKatalogSeeder
{
    /**
     *
     * @param Config $config
     * @return void
     */
    public function __construct(protected Config $config)
    {
    }

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
            ->chunk($this->config->get('idir.import.job_limit'), function ($items) {
                CommentsJob::dispatch($items, $this->userLastId)->onQueue('import');
            });
    }
}
