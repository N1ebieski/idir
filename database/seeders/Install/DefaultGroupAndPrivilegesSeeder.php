<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\IDir\Database\Seeders\Install;

use Illuminate\Database\Seeder;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Privilege;
use N1ebieski\IDir\ValueObjects\Group\Url;
use N1ebieski\IDir\ValueObjects\Group\Visible;
use N1ebieski\IDir\ValueObjects\Group\Backlink;
use N1ebieski\IDir\ValueObjects\Group\ApplyStatus;

class DefaultGroupAndPrivilegesSeeder extends Seeder
{
    /**
     * Run the database Seeders.
     *
     * @return void
     */
    public function run()
    {
        Privilege::firstOrCreate(['name' => 'highest position on homepage']);
        Privilege::firstOrCreate(['name' => 'highest position in their categories']);
        Privilege::firstOrCreate(['name' => 'highest position in ancestor categories']);
        Privilege::firstOrCreate(['name' => 'highest position in search results']);
        Privilege::firstOrCreate(['name' => 'additional link on the friends subpage']);
        Privilege::firstOrCreate(['name' => 'direct link on listings']);
        Privilege::firstOrCreate(['name' => 'place in the links component']);
        Privilege::firstOrCreate(['name' => 'place in the advertising component']);
        Privilege::firstOrCreate(['name' => 'additional options for editing content']);
        Privilege::firstOrCreate(['name' => 'generate content by AI']);

        $nofollow = Privilege::firstOrCreate(['name' => 'direct link nofollow']);

        $default = Group::firstOrCreate(['id' => 1], [
            'name' => 'Default',
            'max_cats' => 3,
            'position' => 0,
            'visible' => Visible::INACTIVE,
            'backlink' => Backlink::INACTIVE,
            'apply_status' => ApplyStatus::INACTIVE,
            'url' => Url::OPTIONAL
        ]);

        $default->privileges()->sync([$nofollow->id]);
    }
}
