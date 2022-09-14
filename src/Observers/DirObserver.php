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

namespace N1ebieski\IDir\Observers;

use BadMethodCallException;
use N1ebieski\IDir\Models\Dir;
use Illuminate\Support\Facades\Cache;

class DirObserver
{
    /**
     * [private description]
     * @var bool
     */
    private static $pivotEvent = false;

    /**
     * Handle the dir "created" event.
     *
     * @param  Dir  $dir
     * @return void
     */
    public function created(Dir $dir)
    {
        Cache::tags(['dirs', 'links', 'categories'])->flush();
    }

    /**
     * Handle the dir "updated" event.
     *
     * @param  Dir  $dir
     * @return void
     */
    public function updated(Dir $dir)
    {
        Cache::tags(['dir.' . $dir->slug, 'dirs', 'links', 'categories'])->flush();
    }

    /**
     *
     * @param Dir $dir
     * @param mixed $relationName
     * @param mixed $pivotIds
     * @param mixed $pivotIdsAttributes
     * @return void
     * @throws BadMethodCallException
     */
    public function pivotUpdated(Dir $dir, $relationName, $pivotIds, $pivotIdsAttributes)
    {
        if (self::$pivotEvent === false && in_array($relationName, ['fields'])) {
            $this->updated($dir);

            self::$pivotEvent = true;
        }
    }

    /**
     *
     * @param Dir $dir
     * @param mixed $relationName
     * @param mixed $pivotIds
     * @param mixed $pivotIdsAttributes
     * @return void
     * @throws BadMethodCallException
     */
    public function pivotAttached(Dir $dir, $relationName, $pivotIds, $pivotIdsAttributes)
    {
        if (self::$pivotEvent === false && in_array($relationName, ['fields', 'categories', 'tags', 'regions'])) {
            $this->updated($dir);

            self::$pivotEvent = true;
        }
    }

    /**
     *
     * @param Dir $dir
     * @param mixed $relationName
     * @param mixed $pivotIds
     * @return void
     * @throws BadMethodCallException
     */
    public function pivotDetached(Dir $dir, $relationName, $pivotIds)
    {
        if (self::$pivotEvent === false && in_array($relationName, ['fields', 'categories', 'tags', 'regions'])) {
            $this->updated($dir);

            self::$pivotEvent = true;
        }
    }

    /**
     * Handle the dir "deleted" event.
     *
     * @param  Dir  $dir
     * @return void
     */
    public function deleted(Dir $dir)
    {
        Cache::tags(['dir.' . $dir->slug, 'dirs', 'links', 'categories'])->flush();
    }

    /**
     * Handle the dir "restored" event.
     *
     * @param  Dir  $dir
     * @return void
     */
    public function restored(Dir $dir)
    {
        //
    }

    /**
     * Handle the dir "force deleted" event.
     *
     * @param  Dir  $dir
     * @return void
     */
    public function forceDeleted(Dir $dir)
    {
        //
    }
}
