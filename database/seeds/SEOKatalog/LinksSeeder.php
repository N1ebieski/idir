<?php

namespace N1ebieski\IDir\Seeds\SEOKatalog;

use N1ebieski\IDir\Models\Link;
use Illuminate\Support\Facades\DB;
use N1ebieski\IDir\Seeds\SEOKatalog\SEOKatalogSeeder;

class LinksSeeder extends SEOKatalogSeeder
{
    /**
     * Run the database seeds.
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
                            $link = Link::create([
                                'type' => $type === 'in' ?
                                    'backlink'
                                    : 'link',
                                'name' => $item->title,
                                'url' => static::url($item->url)
                            ]);

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
    protected static function url(string $url) : string
    {
        return strtolower(strpos($url, 'https://') ? $url : 'http://' . $url);
    }
}
