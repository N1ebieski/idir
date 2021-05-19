<?php

namespace N1ebieski\IDir\Seeds\PHPLD;

use Illuminate\Support\Facades\DB;
use N1ebieski\IDir\Models\BanValue;
use N1ebieski\IDir\Seeds\PHPLD\PHPLDSeeder;

class BansSeeder extends PHPLDSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::connection('import')
            ->table('banlist')
            ->orderBy('ID')
            ->chunk(1000, function ($items) {
                $items->each(function ($item) {
                    DB::transaction(function () use ($item) {
                        if (!empty($item->BAN_DOMAIN)) {
                            BanValue::create([
                                'value' => $item->BAN_DOMAIN,
                                'type' => 'url'
                            ]);
                        }

                        if (!empty($item->BAN_IP)) {
                            BanValue::create([
                                'value' => $item->BAN_IP,
                                'type' => 'ip'
                            ]);
                        }
                    });
                });
            });
    }
}
