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

use N1ebieski\IDir\Models\User;
use Illuminate\Support\Facades\DB;
use N1ebieski\IDir\Database\Seeders\PHPLD\PHPLDSeeder;
use N1ebieski\IDir\Database\Seeders\PHPLD\Jobs\UsersJob;

class UsersSeeder extends PHPLDSeeder
{
    /**
     * Undocumented function
     *
     * @return integer
     */
    protected function getUserLastId(): int
    {
        /** @var User */
        $user = User::orderBy('id', 'desc')->first();

        return $user->id;
    }

    /**
     * Run the database Seeders.
     *
     * @return void
     */
    public function run()
    {
        DB::connection('import')
            ->table('user')
            ->orderBy('ID', 'asc')
            ->chunk(1000, function ($items) {
                $items->map(function ($item) {
                    $item->adres = utf8_encode($item->adres);
                    $item->firma = utf8_encode($item->firma);
                    $item->NAME = utf8_encode($item->NAME);

                    return $item;
                });

                UsersJob::dispatch($items, $this->userLastId)->onQueue('import');
            });
    }
}
