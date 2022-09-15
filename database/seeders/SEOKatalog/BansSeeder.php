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
                        if (!empty($item->url)) {
                            BanValue::create([
                                'value' => $this->getUrl($item->url),
                                'type' => Type::URL
                            ]);
                        }

                        if (!empty($item->ip)) {
                            BanValue::create([
                                'value' => $item->ip,
                                'type' => Type::IP
                            ]);
                        }

                        if (is_int($item->user) && $item->user !== 0) {
                            if ($user = User::find($this->userLastId + $item->user)) {
                                $banModel = new BanModel();

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
    protected function getUrl(string $url): string
    {
        return strpos($url, 'https://') ? $url : 'http://' . $url;
    }
}
