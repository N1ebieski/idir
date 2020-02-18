<?php

namespace N1ebieski\IDir\Seeds;

use Illuminate\Database\Seeder;
use N1ebieski\IDir\Models\Field\Group\Field;

class DefaultFieldsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Field::create([
            'title' => 'Region',
            'type' => 'regions',
            'visible' => 1,
            'options' => ['required' => 0]
        ]);

        Field::create([
            'title' => 'Lokalizacja',
            'type' => 'map',
            'visible' => 1,
            'options' => ['required' => 0]
        ]);
    }
}
