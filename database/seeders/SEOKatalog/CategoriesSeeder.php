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

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use N1ebieski\IDir\Models\Category\Dir\Category;
use N1ebieski\IDir\Database\Seeders\SEOKatalog\SEOKatalogSeeder;

class CategoriesSeeder extends SEOKatalogSeeder
{
    /**
     * Run the database Seeders.
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
                    $category = new Category();

                    $category->id = $this->subLastId + $item->id;
                    $category->name = $item->title;
                    $category->status = $item->active;
                    // @phpstan-ignore-next-line
                    $category->created_at = Carbon::createFromTimestamp($item->date);
                    // @phpstan-ignore-next-line
                    $category->updated_at = Carbon::createFromTimestamp($item->date);

                    $category->save();
                });
            });

        DB::connection('import')->table('subcategories')
            ->orderBy('title', 'asc')
            ->get()
            ->each(function ($item) {
                DB::transaction(function () use ($item) {
                    $category = new Category();

                    $category->id = $item->id;
                    $category->name = $item->title;
                    $category->status = $item->active;
                    $category->parent_id = $this->subLastId + $item->id_cat;
                    // @phpstan-ignore-next-line
                    $category->created_at = Carbon::createFromTimestamp($item->date);
                    // @phpstan-ignore-next-line
                    $category->updated_at = Carbon::createFromTimestamp($item->date);

                    $category->save();
                });
            });
    }
}
