<?php

namespace N1ebieski\IDir\Seeds\SEOKatalog;

use N1ebieski\IDir\Seeds\SEOKatalogSeeder;
use N1ebieski\IDir\Models\BanValue;
use N1ebieski\IDir\Models\BanModel\Dir\BanModel;
use N1ebieski\IDir\Models\User;
use Illuminate\Support\Facades\DB;

class BansSeeder extends SEOKatalogSeeder
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
        DB::connection('import')
            ->table('spam')
            ->orderBy('id')
            ->chunk(1000, function($items) {
                $items->each(function($item) {
                    if (!empty($item->url)) {
                        BanValue::create([
                            'value' => $this->makeUrl($item->url),
                            'type' => 'url'
                        ]);
                    }

                    if (!empty($item->ip)) {
                        BanValue::create([
                            'value' => $item->ip,
                            'type' => 'ip'
                        ]);
                    } 
                    
                    if (is_int($item->user) && $item->user !== 0) {
                        if ($user = User::find($this->user_last_id + $item->user)) {
                            BanModel::make()->morph()->associate($user)->save();
                        }
                    }                      
                });
            });
    }
}
