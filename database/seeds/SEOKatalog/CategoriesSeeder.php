<?php

namespace N1ebieski\IDir\Seeds\SEOKatalog;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use N1ebieski\IDir\Models\Category\Dir\Category;
use N1ebieski\IDir\Seeds\SEOKatalog\SEOKatalogSeeder;

class CategoriesSeeder extends SEOKatalogSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::connection('import')->table('categories')
            ->orderBy('position', 'asc')
            ->orderBy('title', 'asc')
            ->get()
            ->each(function ($item) {
                DB::transaction(function () use ($item) {
                    $category = Category::make();

                    $category->id = $this->subLastId + $item->id;
                    $category->name = $item->title;
                    $category->status = $item->active;
                    $category->created_at = Carbon::createFromTimestamp($item->date);
                    $category->updated_at = Carbon::createFromTimestamp($item->date);

                    $category->save();
                });
            });

        DB::connection('import')->table('subcategories')
            ->orderBy('title', 'asc')
            ->get()
            ->each(function ($item) {
                DB::transaction(function () use ($item) {
                    $category = Category::make();

                    $category->id = $item->id;
                    $category->name = $item->title;
                    $category->status = $item->active;
                    $category->parent_id = $this->subLastId + $item->id_cat;
                    $category->created_at = Carbon::createFromTimestamp($item->date);
                    $category->updated_at = Carbon::createFromTimestamp($item->date);

                    $category->save();
                });
            });
    }
}
