<?php

namespace N1ebieski\IDir\Seeds\PHPLD;

use N1ebieski\IDir\Seeds\PHPLD\PHPLDSeeder;
use N1ebieski\IDir\Models\Group;
use Illuminate\Support\Facades\DB;
use N1ebieski\IDir\Models\Privilege;

class GroupsAndPrivilegesSeeder extends PHPLDSeeder
{
    /**
     * Run the database seeds.
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
                    $group->alt_id = Group::DEFAULT;
                    $group->desc = strlen($item->DESCRIPTION) > 0 ?
                        strip_tags($item->DESCRIPTION)
                        : null;
                    $group->max_cats = $item->MULTIPLE_CATEGORIES === 0 ? 1 : 3;
                    $group->max_models = null;
                    $group->max_models_daily = null;
                    $group->visible = $item->STATUS === 0 ?
                        Group::INVISIBLE
                        : Group::VISIBLE;
                    $group->apply_status = $item->REQUIRE_APPROVAL === 1 ?
                        Group::APPLY_INACTIVE
                        : Group::APPLY_ACTIVE;
                    $group->url = Group::OBLIGATORY_URL;
                    $group->backlink = Group::WITHOUT_BACKLINK;

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
    protected static function groupLastId() : int
    {
        return Group::orderBy('id', 'desc')->first()->id;
    }    
}
