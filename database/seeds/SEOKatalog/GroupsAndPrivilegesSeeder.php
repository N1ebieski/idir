<?php

namespace N1ebieski\IDir\Seeds\SEOKatalog;

use N1ebieski\IDir\Seeds\SEOKatalog\SEOKatalogSeeder;
use N1ebieski\IDir\Models\Group;
use Illuminate\Support\Facades\DB;
use N1ebieski\IDir\Models\Privilege;

class GroupsAndPrivilegesSeeder extends SEOKatalogSeeder
{
    /**
     * Undocumented function
     *
     * @return integer
     */
    protected static function makeGroupLastId() : int
    {
        return Group::orderBy('id', 'desc')->first()->id;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $groups = DB::connection('import')->table('groups')
            ->orderBy('position', 'asc')->orderBy('title', 'asc')->get();

        $privileges = Privilege::all();

        $groups->each(function ($item) use ($privileges) {
            $group = Group::create([
                'id' => $this->group_last_id + $item->id,
                'name' => $item->title,
                'alt_id' => $this->group_last_id + $item->alt_group,
                'desc' => strlen($item->description) > 0 ? $item->description : null,
                'max_cats' => $item->cat,
                'max_models' => $item->max_sites !== 0 ? $item->max_sites : null,
                'max_models_daily' => $item->max!== 0 ? $item->max : null,
                'visible' => $item->type,
                'apply_status' => 0,
                'url' => 2,
                'backlink' => $item->backlink === 0 ? 0 : ($item->backlink === 2 ? 1 : 2)
            ]);
            
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
    }
}
