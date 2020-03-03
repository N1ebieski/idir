<?php

namespace N1ebieski\IDir\Seeds\SEOKatalog;

use N1ebieski\IDir\Seeds\SEOKatalogSeeder;
use N1ebieski\IDir\Models\Category\Dir\Category;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CategoriesSeeder extends SEOKatalogSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $subcategories = DB::connection('import')->table('subcategories')
            ->orderBy('title', 'asc')->get();

        $categories = DB::connection('import')->table('categories')->orderBy('position', 'asc')
            ->orderBy('title', 'asc')->get();

        $categories->each(function ($item) {
            Category::create([
                'id' => $this->sub_last_id + $item->id,
                'name' => $item->title,
                'status' => $item->active,
                'created_at' => Carbon::createFromTimestamp($item->date),
                'updated_at' => Carbon::createFromTimestamp($item->date)
            ]);
        });

        $subcategories->each(function ($item) {
            Category::create([
                'id' => $item->id,
                'name' => $item->title,
                'status' => $item->active,
                'parent_id' => $this->sub_last_id + $item->id_cat,
                'created_at' => Carbon::createFromTimestamp($item->date),
                'updated_at' => Carbon::createFromTimestamp($item->date)
            ]);
        });
    }
}
