<?php

namespace N1ebieski\IDir\Database\Seeders\Install;

use Illuminate\Database\Seeder;
use N1ebieski\ICore\Models\Stat\Stat;
use N1ebieski\ICore\ValueObjects\Stat\Slug;

class DefaultStatsSeeder extends Seeder
{
    /**
     * Run the database Seeders.
     *
     * @return void
     */
    public function run()
    {
        Stat::firstOrCreate(['slug' => Slug::CLICK]);
    }
}
