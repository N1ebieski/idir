<?php

namespace N1ebieski\IDir\Database\Seeders\SEOKatalog;

use N1ebieski\IDir\Models\Link;
use Illuminate\Support\Facades\DB;
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
                            $link = Link::make();

                            $link->type = $type === 'in' ? 'backlink' : 'link';
                            $link->name = $item->title;
                            $link->url = static::url($item->url);

                            $link->save();

                            $link->categories()->attach(
                                $item->cat === 'all' ?
                                null
                                : collect(array_filter(explode(',', $item->cat)))
                                    ->filter(function ($item) {
                                        return $item > 0;
                                    })
                                    ->map(function ($item) {
                                        return $this->subLastId + $item;
                                    })
                                    ->toArray()
                            );
                        }
                    }
                });
            });
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @return string
     */
    protected function url(string $url): string
    {
        return strtolower(strpos($url, 'https://') ? $url : 'http://' . $url);
    }
}
