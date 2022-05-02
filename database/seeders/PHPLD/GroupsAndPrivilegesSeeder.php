<?php

namespace N1ebieski\IDir\Database\Seeders\PHPLD;

use N1ebieski\IDir\Models\Group;
use Illuminate\Support\Facades\DB;
use N1ebieski\IDir\Models\Privilege;
use N1ebieski\IDir\ValueObjects\Group\Id;
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

        DB::connection('import')->table('link_type')
            ->orderBy('ORDER_ID', 'asc')
            ->orderBy('ID', 'asc')
            ->get()
            ->each(function ($item) use ($privileges) {
                DB::transaction(function () use ($item, $privileges) {
                    $group = Group::make();

                    $group->id = $this->groupLastId + $item->ID;
                    $group->name = $item->NAME;
                    $group->alt_id = $group->makeCache()->rememberBySlug(Slug::default())->id;
                    $group->desc = strlen($item->DESCRIPTION) > 0 ?
                        strip_tags($item->DESCRIPTION)
                        : null;
                    $group->max_cats = $item->MULTIPLE_CATEGORIES === 0 ? 1 : 3;
                    $group->max_models = null;
                    $group->max_models_daily = null;
                    $group->visible = $item->STATUS === 0 ?
                        Visible::INACTIVE
                        : Visible::ACTIVE;
                    $group->apply_status = $item->REQUIRE_APPROVAL === 1 ?
                        ApplyStatus::INACTIVE
                        : ApplyStatus::ACTIVE;
                    $group->url = Url::ACTIVE;
                    $group->backlink = Backlink::INACTIVE;

                    $group->save();

                    $privIds = array();

                    if ($item->FEATURED === 1) {
                        $privIds[] = $privileges->where('name', 'highest position in their categories')->first()->id;
                    }

                    if ($item->FEATURED === 1) {
                        $privIds[] = $privileges->where('name', 'highest position in ancestor categories')->first()->id;
                    }

                    if ($item->FEATURED === 1) {
                        $privIds[] = $privileges->where('name', 'highest position in search results')->first()->id;
                    }

                    if ($item->NOFOLLOW === 1) {
                        $privIds[] = $privileges->where('name', 'direct link nofollow')->first()->id;
                    }

                    if ($item->FEATURED === 1) {
                        $privIds[] = $privileges->where('name', 'place in the advertising component')->first()->id;
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
    protected static function groupLastId(): int
    {
        return Group::orderBy('id', 'desc')->first()->id;
    }
}
