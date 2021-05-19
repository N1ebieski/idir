<?php

namespace N1ebieski\IDir\Seeds\SEOKatalog;

use N1ebieski\IDir\Models\Group;
use Illuminate\Support\Facades\DB;
use N1ebieski\IDir\Models\Privilege;
use N1ebieski\IDir\Seeds\SEOKatalog\SEOKatalogSeeder;

class GroupsAndPrivilegesSeeder extends SEOKatalogSeeder
{
    /**
     * Run the database seeds.
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
                    $group = Group::make();

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
                    $group->apply_status = Group::APPLY_INACTIVE;
                    $group->url = Group::OBLIGATORY_URL;
                    $group->backlink = $item->backlink === 0 ?
                        Group::WITHOUT_BACKLINK
                        : ($item->backlink === 2 ? Group::OPTIONAL_BACKLINK : Group::OBLIGATORY_BACKLINK);
                    
                    $group->save();

                    $privIds = array();

                    if ($item->home === 1) {
                        $privIds[] = $privileges->where('name', 'highest position on homepage')->first()->id;
                    }

                    if ($item->my_cat === 1 || $item->my_sub === 1) {
                        $privIds[] = $privileges->where('name', 'highest position in their categories')->first()->id;
                    }

                    if ($item->all_cat === 1 || $item->all_sub === 1) {
                        $privIds[] = $privileges->where('name', 'highest position in ancestor categories')->first()->id;
                    }

                    if ($item->friend === 1) {
                        $privIds[] = $privileges->where('name', 'additional link on the friends subpage')->first()->id;
                    }

                    if ($item->my_search === 1) {
                        $privIds[] = $privileges->where('name', 'highest position in search results')->first()->id;
                    }

                    if ($item->sites_link === 1) {
                        $privIds[] = $privileges->where('name', 'direct link on listings')->first()->id;
                    }

                    if ($item->nofollow === 1) {
                        $privIds[] = $privileges->where('name', 'direct link nofollow')->first()->id;
                    }

                    if ($item->link_box === 1) {
                        $privIds[] = $privileges->where('name', 'place in the links component')->first()->id;
                    }
                    
                    if ($item->premium_box === 1) {
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
