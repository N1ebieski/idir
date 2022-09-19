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

namespace N1ebieski\IDir\Database\Seeders\PHPLD;

use N1ebieski\IDir\Models\Group;
use Illuminate\Support\Facades\DB;
use N1ebieski\IDir\Models\Privilege;
use N1ebieski\IDir\ValueObjects\Group\Url;
use N1ebieski\IDir\ValueObjects\Group\Slug;
use N1ebieski\IDir\ValueObjects\Group\Visible;
use N1ebieski\IDir\ValueObjects\Group\Backlink;
use N1ebieski\IDir\ValueObjects\Group\ApplyStatus;
use N1ebieski\IDir\Database\Seeders\PHPLD\PHPLDSeeder;

class GroupsAndPrivilegesSeeder extends PHPLDSeeder
{
    /**
     * Run the database Seeders.
     *
     * @return void
     */
    public function run()
    {
        $privileges = Privilege::all();

        /** @var Group */
        $defaultGroup = Group::make()->makeCache()->rememberBySlug(Slug::default());

        DB::connection('import')->table('link_type')
            ->orderBy('ORDER_ID', 'asc')
            ->orderBy('ID', 'asc')
            ->get()
            ->each(function ($item) use ($privileges, $defaultGroup) {
                DB::transaction(function () use ($item, $privileges, $defaultGroup) {
                    $group = new Group();

                    $group->id = $this->groupLastId + $item->ID;
                    $group->name = $item->NAME;
                    $group->alt_id = $defaultGroup->id;
                    $group->desc = strlen($item->DESCRIPTION) > 0 ?
                        strip_tags($item->DESCRIPTION)
                        : null;
                    $group->max_cats = $item->MULTIPLE_CATEGORIES === 0 ? 1 : 3;
                    $group->max_models = null;
                    $group->max_models_daily = null;
                    $group->visible = $item->STATUS === 0 ?
                        Visible::inactive()
                        : Visible::active();
                    $group->apply_status = $item->REQUIRE_APPROVAL === 1 ?
                        ApplyStatus::inactive()
                        : ApplyStatus::active();
                    $group->url = Url::active();
                    $group->backlink = Backlink::inactive();

                    $group->save();

                    $privIds = array();

                    if ($item->FEATURED === 1) {
                        /** @var Privilege */
                        $privilege = $privileges->where('name', 'highest position in their categories')->first();

                        $privIds[] = $privilege->id;
                    }

                    if ($item->FEATURED === 1) {
                        /** @var Privilege */
                        $privilege = $privileges->where('name', 'highest position in ancestor categories')->first();

                        $privIds[] = $privilege->id;
                    }

                    if ($item->FEATURED === 1) {
                        /** @var Privilege */
                        $privilege = $privileges->where('name', 'highest position in search results')->first();

                        $privIds[] = $privilege->id;
                    }

                    if ($item->NOFOLLOW === 1) {
                        /** @var Privilege */
                        $privilege = $privileges->where('name', 'direct link nofollow')->first();

                        $privIds[] = $privilege->id;
                    }

                    if ($item->FEATURED === 1) {
                        /** @var Privilege */
                        $privilege = $privileges->where('name', 'place in the advertising component')->first();

                        $privIds[] = $privilege->id;
                    }

                    $group->privileges()->attach($privIds);
                });
            });
    }

    /**
     * Undocumented function
     *
     * @return integer
     */
    protected function getGroupLastId(): int
    {
        /** @var Group */
        $group = Group::orderBy('id', 'desc')->first();

        return $group->id;
    }
}
