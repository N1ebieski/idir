<?php

namespace N1ebieski\IDir\Database\Seeders\Install;

use Illuminate\Database\Seeder;
use N1ebieski\IDir\Models\Field\Group\Field;

class DefaultFieldsSeeder extends Seeder
{
    /**
     * Run the database Seeders.
     *
     * @return void
     */
    public function run()
    {
        Field::create([
            'title' => 'Region',
            'type' => 'regions',
            'visible' => Field::VISIBLE,
            'options' => ['required' => 0]
        ]);

        Field::create([
            'title' => 'Lokalizacja',
            'type' => 'map',
            'visible' => Field::VISIBLE,
            'options' => ['required' => 0]
        ]);
    }
}
