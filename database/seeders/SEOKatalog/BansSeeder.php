<?php

namespace N1ebieski\IDir\Database\Seeders\SEOKatalog;

use N1ebieski\IDir\Models\User;
use Illuminate\Support\Facades\DB;
use N1ebieski\IDir\Models\BanValue;
use N1ebieski\IDir\Models\BanModel\Dir\BanModel;
use N1ebieski\IDir\Database\Seeders\SEOKatalog\SEOKatalogSeeder;

class BansSeeder extends SEOKatalogSeeder
{
    /**
     * Run the database Seeders.
     *
     * @return void
     */
    public function run()
    {
        DB::connection('import')
            ->table('spam')
            ->orderBy('id')
            ->chunk(1000, function ($items) {
                $items->each(function ($item) {
                    DB::transaction(function () use ($item) {
                        if (!empty($item->url)) {
                            BanValue::create([
                                'value' => $this->url($item->url),
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
                            if ($user = User::find($this->userLastId + $item->user)) {
                                BanModel::make()->morph()->associate($user)->save();
                            }
                        }
                    });
                });
            });
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @return string
     */
    protected static function url(string $url): string
    {
        return strpos($url, 'https://') ? $url : 'http://' . $url;
    }
}
