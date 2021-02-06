<?php

namespace N1ebieski\IDir\Seeds\SEOKatalog;

use Illuminate\Support\Str;
use N1ebieski\IDir\Models\User;
use Illuminate\Support\Facades\DB;
use N1ebieski\IDir\Seeds\SEOKatalog\SEOKatalogSeeder;

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
            ->chunk(1000, function ($items) {
                $items->each(function ($item) {
                    $name = User::firstWhere('name', '=', $item->nick) === null ?
                        $item->nick : 'user-' . Str::uuid();

                    $user = User::firstOrCreate(
                        [
                            'email' => $item->email
                        ],
                        [
                            'id' => $this->user_last_id + $item->id,
                            'name' => $name,
                            'password' => Str::random(12),
                            'status' => $item->active
                        ]
                    );
        
                    $user->assignRole('user');
                });
            });
    }
}
