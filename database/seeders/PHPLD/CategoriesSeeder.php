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

use Illuminate\Support\Facades\DB;
use N1ebieski\IDir\Models\Category\Dir\Category;
use N1ebieski\ICore\ValueObjects\Category\Status;
use N1ebieski\IDir\Database\Seeders\PHPLD\PHPLDSeeder;

class CategoriesSeeder extends PHPLDSeeder
{
    /**
     * Run the database Seeders.
     *
     * @return void
     */
    public function run()
    {
        DB::connection('import')
            ->table('category')
            ->orderBy('PARENT_ID', 'asc')
            ->orderBy('TITLE', 'asc')
            ->get()
            ->each(function ($item) {
                DB::transaction(function () use ($item) {
                    $category = new Category();

                    $category->id = $item->ID;
                    $category->name = $item->TITLE;
                    $category->status = $item->STATUS === 0 ?
                        Status::inactive()
                        : Status::active();
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
