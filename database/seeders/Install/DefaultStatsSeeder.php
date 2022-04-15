<?php

namespace N1ebieski\IDir\Database\Seeders\Install;

use Illuminate\Database\Seeder;
use N1ebieski\ICore\Models\Stat\Stat;

class DefaultStatsSeeder extends Seeder
{
    /**
     * Run the database Seeders.
     *
     * @return void
     */
    public function run()
    {
        Stat::firstOrCreate(['slug' => Stat::CLICK]);
    }
}
