<?php

namespace N1ebieski\IDir\Seeds;

use Illuminate\Database\Seeder;
use N1ebieski\IDir\Models\Region\Region;

class RegionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $regions = [
            'Dolnośląskie',
            'Kujawsko-pomorskie',
            'Lubelskie',
            'Lubuskie',
            'Łódzkie',
            'Małopolskie',
            'Mazowieckie',
            'Opolskie',
            'Podkarpackie',
            'Podlaskie',
            'Pomorskie',
            'Śląskie',
            'Świętokrzyskie',
            'Warmińsko-mazurskie',
            'Wielkopolskie',
            'Zachodniopomorskie'
        ];

        foreach ($regions as $region) {
            Region::create(['name' => $region]);
        }
    }
}
