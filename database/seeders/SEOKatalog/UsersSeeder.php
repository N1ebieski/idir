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

use N1ebieski\IDir\Models\User;
use Illuminate\Support\Facades\DB;
use N1ebieski\IDir\Database\Seeders\SEOKatalog\Jobs\UsersJob;
use N1ebieski\IDir\Database\Seeders\SEOKatalog\SEOKatalogSeeder;

class UsersSeeder extends SEOKatalogSeeder
{
    /**
     * Run the database Seeders.
     *
     * @return void
     */
    public function run()
    {
        DB::connection('import')
            ->table('users')
            ->orderBy('id', 'asc')
            ->chunk(1000, function ($items) {
                UsersJob::dispatch($items, $this->userLastId)->onQueue('import');
            });
    }

    /**
     * Undocumented function
     *
     * @return integer
     */
    protected function getUserLastId(): int
    {
        return User::orderBy('id', 'desc')->first()->id;
    }
}
