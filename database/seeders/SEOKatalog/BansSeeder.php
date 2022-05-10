<?php

namespace N1ebieski\IDir\Database\Seeders\SEOKatalog;

use N1ebieski\IDir\Models\User;
use Illuminate\Support\Facades\DB;
use N1ebieski\IDir\Models\BanValue;
use N1ebieski\IDir\ValueObjects\BanValue\Type;
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
                        /**
                         * @var BanValue
                         */
                        $banValue = BanValue::make();

                        if (!empty($item->url)) {
                            $banValue->create([
                                'value' => $this->url($item->url),
                                'type' => Type::URL
                            ]);
                        }

                        if (!empty($item->ip)) {
                            $banValue->create([
                                'value' => $item->ip,
                                'type' => Type::IP
                            ]);
                        }

                        if (is_int($item->user) && $item->user !== 0) {
                            if ($user = User::find($this->userLastId + $item->user)) {
                                /**
                                 * @var BanModel
                                 */
                                $banModel = BanModel::make();

                                $banModel->morph()->associate($user)->save();
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
    protected function url(string $url): string
    {
        return strpos($url, 'https://') ? $url : 'http://' . $url;
    }
}
