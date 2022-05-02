<?php

namespace N1ebieski\IDir\Database\Seeders\Install;

use Illuminate\Database\Seeder;
use N1ebieski\IDir\ValueObjects\Field\Type;
use N1ebieski\IDir\Models\Field\Group\Field;
use N1ebieski\IDir\ValueObjects\Field\Visible;
use N1ebieski\IDir\ValueObjects\Field\Required;

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
            'type' => Type::REGIONS,
            'visible' => Visible::ACTIVE,
            'options' => ['required' => Required::INACTIVE]
        ]);

        Field::create([
            'title' => 'Lokalizacja',
            'type' => Type::MAP,
            'visible' => Visible::ACTIVE,
            'options' => ['required' => Required::INACTIVE]
        ]);
    }
}
