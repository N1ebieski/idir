<?php

namespace N1ebieski\IDir\Seeds\SEOKatalog;

use N1ebieski\IDir\Seeds\SEOKatalog\SEOKatalogSeeder;
use N1ebieski\IDir\Models\Link;
use Illuminate\Support\Facades\DB;

class LinksSeeder extends SEOKatalogSeeder
{
    /**
     * Undocumented function
     *
     * @param string $url
     * @return string
     */
    protected static function makeUrl(string $url) : string
    {
        return strpos($url, 'https://') ? $url : 'http://' . $url;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $links = DB::connection('import')->table('links')
            ->orderBy('position', 'asc')->orderBy('title', 'asc')->get();

        $links->each(function ($item) {
            foreach (['in', 'out'] as $type) {
                if ($item->{$type} !== 0) {
                    $link = Link::create([
                        'type' => $type === 'in' ? 'backlink' : 'link',
                        'name' => $item->title,
                        'url' => $this->makeUrl($item->url)
                    ]);

                    $link->categories()->attach(
                        $item->cat === 'all' ?
                        null :
                        collect(array_filter(explode(',', $item->cat)))
                            ->filter(function ($item) {
                                return $item > 0;
                            })
                            ->map(function ($item) {
                                return $this->sub_last_id + $item;
                            })
                            ->toArray()
                    );
                }
            }
        });
    }
}
