<?php

namespace N1ebieski\IDir\Seeds\PHPLD;

use Illuminate\Support\Facades\DB;
use N1ebieski\IDir\Seeds\PHPLD\PHPLDSeeder;
use N1ebieski\IDir\Models\Category\Dir\Category;

class CategoriesSeeder extends PHPLDSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::connection('import')
            ->table('category')
            ->orderBy('PARENT_ID', 'asc')
            ->orderBy('ID', 'asc')
            ->get()
            ->each(function ($item) {
                DB::transaction(function () use ($item) {
                    $category = Category::make();

                    $category->id = $item->ID;
                    $category->name = $item->TITLE;
                    $category->status = $item->STATUS === 0 ?
                        Category::INACTIVE
                        : Category::ACTIVE;
                    $category->parent_id = !empty($item->PARENT_ID) && Category::find($item->PARENT_ID) !== null ?
                        $item->PARENT_ID
                        : null;
                    $category->created_at = $item->DATE_ADDED;
                    $category->updated_at = $item->DATE_ADDED;

                    $category->save();
                });
            });
    }
}
