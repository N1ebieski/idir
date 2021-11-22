<?php

namespace N1ebieski\IDir\Observers;

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
     * Undocumented function
     *
     * @param Dir $dir
     * @param [type] $relationName
     * @param [type] $pivotIds
     * @param [type] $pivotIdsAttributes
     * @return void
     */
    public function pivotUpdated(Dir $dir, $relationName, $pivotIds, $pivotIdsAttributes)
    {
        if (static::$pivotEvent === false && in_array($relationName, ['fields'])) {
            $this->updated($dir);

            static::$pivotEvent = true;
        }
    }

    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param [type] $relationName
     * @param [type] $pivotIds
     * @param [type] $pivotIdsAttributes
     * @return void
     */
    public function pivotAttached(Dir $dir, $relationName, $pivotIds, $pivotIdsAttributes)
    {
        if (static::$pivotEvent === false && in_array($relationName, ['fields', 'categories', 'tags', 'regions'])) {
            $this->updated($dir);

            static::$pivotEvent = true;
        }
    }

    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param [type] $relationName
     * @param [type] $pivotIds
     * @return void
     */
    public function pivotDetached(Dir $dir, $relationName, $pivotIds)
    {
        if (static::$pivotEvent === false && in_array($relationName, ['fields', 'categories', 'tags', 'regions'])) {
            $this->updated($dir);

            static::$pivotEvent = true;
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
