<?php

namespace N1ebieski\IDir\Seeds\SEOKatalog;

use N1ebieski\IDir\Seeds\SEOKatalogSeeder;
use N1ebieski\IDir\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UsersSeeder extends SEOKatalogSeeder
{
    /**
     * Undocumented function
     *
     * @return integer
     */
    protected static function makeUserLastId() : int
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
            ->table('users')
            ->orderBy('id', 'asc')
            ->chunk(1000, function($items) {
                $items->each(function($item) {
                    $user = User::firstOrCreate(
                        [
                            'email' => $item->email
                        ], [
                            'id' => $this->user_last_id + $item->id,
                            'name' => $item->nick,
                            'status' => $item->active
                        ]
                    );
        
                    $user->assignRole('user');
                });
            });
    }
}
