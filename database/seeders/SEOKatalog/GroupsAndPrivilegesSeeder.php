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

namespace N1ebieski\IDir\Database\Seeders\SEOKatalog;

use N1ebieski\IDir\Models\Group;
use Illuminate\Support\Facades\DB;
use N1ebieski\IDir\Models\Privilege;
use N1ebieski\IDir\ValueObjects\Group\Url;
use N1ebieski\IDir\ValueObjects\Group\Backlink;
use N1ebieski\IDir\ValueObjects\Group\ApplyStatus;
use N1ebieski\IDir\Database\Seeders\SEOKatalog\SEOKatalogSeeder;

class GroupsAndPrivilegesSeeder extends SEOKatalogSeeder
{
    /**
     * Run the database Seeders.
     *
     * @return void
     */
    public function run()
    {
        $privileges = Privilege::all();

        DB::connection('import')->table('groups')
            ->orderBy('position', 'asc')
            ->orderBy('title', 'asc')
            ->get()
            ->each(function ($item) use ($privileges) {
                DB::transaction(function () use ($item, $privileges) {
                    $group = new Group();

                    $group->id = $this->groupLastId + $item->id;
                    $group->name = $item->title;
                    $group->alt_id = $this->groupLastId + $item->alt_group;
                    $group->desc = strlen($item->description) > 0 ?
                        $item->description
                        : null;
                    $group->max_cats = $item->cat;
                    $group->max_models = $item->max_sites !== 0 ?
                        $item->max_sites
                        : null;
                    $group->max_models_daily = $item->max !== 0 ?
                        $item->max
                        : null;
                    $group->visible = $item->type;
                    $group->apply_status = ApplyStatus::inactive();
                    $group->url = Url::active();
                    $group->backlink = $item->backlink === 0 ?
                        Backlink::inactive()
                        : ($item->backlink === 2 ? Backlink::optional() : Backlink::active());

                    $group->save();

                    $privIds = array();

                    if ($item->home === 1) {
                        /** @var Privilege */
                        $privilege = $privileges->where('name', 'highest position on homepage')->first();

                        $privIds[] = $privilege->id;
                    }

                    if ($item->my_cat === 1 || $item->my_sub === 1) {
                        /** @var Privilege */
                        $privilege = $privileges->where('name', 'highest position in their categories')->first();

                        $privIds[] = $privilege->id;
                    }

                    if ($item->all_cat === 1 || $item->all_sub === 1) {
                        /** @var Privilege */
                        $privilege = $privileges->where('name', 'highest position in ancestor categories')->first();

                        $privIds[] = $privilege->id;
                    }

                    if ($item->friend === 1) {
                        /** @var Privilege */
                        $privilege = $privileges->where('name', 'additional link on the friends subpage')->first();

                        $privIds[] = $privilege->id;
                    }

                    if ($item->my_search === 1) {
                        /** @var Privilege */
                        $privilege = $privileges->where('name', 'highest position in search results')->first();

                        $privIds[] = $privilege->id;
                    }

                    if ($item->sites_link === 1) {
                        /** @var Privilege */
                        $privilege = $privileges->where('name', 'direct link on listings')->first();

                        $privIds[] = $privilege->id;
                    }

                    if ($item->nofollow === 1) {
                        /** @var Privilege */
                        $privilege = $privileges->where('name', 'direct link nofollow')->first();

                        $privIds[] = $privilege->id;
                    }

                    if ($item->link_box === 1) {
                        /** @var Privilege */
                        $privilege = $privileges->where('name', 'place in the links component')->first();

                        $privIds[] = $privilege->id;
                    }

                    if ($item->premium_box === 1) {
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
