<?php

namespace N1ebieski\IDir\Seeds\PHPLD;

use N1ebieski\IDir\Models\User;
use Illuminate\Support\Facades\DB;
use N1ebieski\IDir\Seeds\PHPLD\PHPLDSeeder;
use N1ebieski\IDir\Seeders\PHPLD\Jobs\UsersJob;

class UsersSeeder extends PHPLDSeeder
{
    /**
     * Undocumented function
     *
     * @return integer
     */
    protected static function userLastId() : int
    {
        return User::orderBy('id', 'desc')->first()->id;
    }

    /**
     * Run the database seeds.
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
