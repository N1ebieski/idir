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

namespace N1ebieski\IDir\Observers\Rating\Dir;

use Illuminate\Support\Facades\Cache;
use N1ebieski\IDir\Models\Rating\Dir\Rating;

class RatingObserver
{
    /**
     * Handle the rating "created" event.
     *
     * @param  Rating  $rating
     * @return void
     */
    public function created(Rating $rating)
    {
        Cache::tags(["{$rating->poli}.{$rating->morph->slug}"])->flush();
    }

    /**
     * Handle the rating "updated" event.
     *
     * @param  Rating  $rating
     * @return void
     */
    public function updated(Rating $rating)
    {
        Cache::tags(["{$rating->poli}.{$rating->morph->slug}"])->flush();
    }

    /**
     * Handle the rating "deleted" event.
     *
     * @param  Rating  $rating
     * @return void
     */
    public function deleted(Rating $rating)
    {
        Cache::tags(["{$rating->poli}.{$rating->morph->slug}"])->flush();
    }

    /**
     * Handle the rating "restored" event.
     *
     * @param  Rating  $rating
     * @return void
     */
    public function restored(Rating $rating)
    {
        //
    }

    /**
     * Handle the rating "force deleted" event.
     *
     * @param  Rating  $dir
     * @return void
     */
    public function forceDeleted(Rating $dir)
    {
        //
    }
}
