<?php

namespace N1ebieski\IDir\Seeds\SEOKatalog;

use N1ebieski\IDir\Models\User;
use Illuminate\Support\Facades\DB;
use N1ebieski\IDir\Seeds\SEOKatalog\Jobs\UsersJob;
use N1ebieski\IDir\Seeds\SEOKatalog\SEOKatalogSeeder;

class UsersSeeder extends SEOKatalogSeeder
{
    /**
     * Run the database seeds.
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
    protected static function userLastId() : int
    {
        return User::orderBy('id', 'desc')->first()->id;
    }
}
