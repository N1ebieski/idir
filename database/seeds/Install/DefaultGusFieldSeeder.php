<?php

namespace N1ebieski\IDir\Seeds\Install;

use Illuminate\Database\Seeder;
use N1ebieski\IDir\Models\Field\Group\Field;

class DefaultGusFieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Field::firstOrCreate([
            'title' => 'Wyszukaj w GUS',
            'desc' => 'Wyszukiwanie za pomocą numeru NIP, KRS lub REGON',
            'type' => 'gus',
            'visible' => Field::VISIBLE,
            'options' => ['required' => 0]
        ]);
    }
}
