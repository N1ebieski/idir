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

use N1ebieski\IDir\Models\Link;
use Illuminate\Support\Facades\DB;
use N1ebieski\ICore\ValueObjects\Link\Type;
use N1ebieski\IDir\Database\Seeders\SEOKatalog\SEOKatalogSeeder;

class LinksSeeder extends SEOKatalogSeeder
{
    /**
     * Run the database Seeders.
     *
     * @return void
     */
    public function run()
    {
        DB::connection('import')->table('links')
            ->orderBy('position', 'asc')
            ->orderBy('title', 'asc')
            ->get()
            ->each(function ($item) {
                DB::transaction(function () use ($item) {
                    foreach (['in', 'out'] as $type) {
                        if ($item->{$type} !== 0) {
                            $link = new Link();

                            $link->type = $this->getType($type);
                            $link->name = $item->title;
                            $link->url = $this->getUrl($item->url);

                            $link->save();

                            $link->categories()->attach(
                                $item->cat === 'all' ?
                                null
                                : collect(array_filter(explode(',', $item->cat)))
                                    ->filter(function ($item) {
                                        return $item > 0;
                                    })
                                    ->map(function ($item) {
                                        return $this->subLastId + (int)$item;
                                    })
                                    ->toArray()
                            );
                        }
                    }
                });
            });
    }

    /**
     *
     * @param string $type
     * @return Type
     */
    protected function getType(string $type): Type
    {
        return new Type($type === 'in' ? Type::BACKLINK : Type::LINK);
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @return string
     */
    protected function getUrl(string $url): string
    {
        return strtolower(strpos($url, 'https://') ? $url : 'http://' . $url);
    }
}
