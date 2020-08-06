<?php

namespace N1ebieski\IDir\Observers;

use Illuminate\Support\Facades\Cache;
use N1ebieski\IDir\Models\Dir;

/**
 * [DirObserver description]
 */
class DirObserver
{
    /**
     * Handle the post "created" event.
     *
     * @param  Dir  $dir
     * @return void
     */
    public function created(Dir $dir)
    {
        Cache::tags(['dirs', 'links', 'categories'])->flush();
    }

    /**
     * Handle the post "updated" event.
     *
     * @param  Dir  $dir
     * @return void
     */
    public function updated(Dir $dir)
    {
        Cache::tags(['dir.'.$dir->slug, 'dirs', 'links', 'categories'])->flush();
    }

    /**
     * Handle the post "deleted" event.
     *
     * @param  Dir  $dir
     * @return void
     */
    public function deleted(Dir $dir)
    {
        Cache::tags(['dir.'.$dir->slug, 'dirs', 'links', 'categories'])->flush();
    }

    /**
     * Handle the post "restored" event.
     *
     * @param  Dir  $dir
     * @return void
     */
    public function restored(Dir $dir)
    {
        //
    }

    /**
     * Handle the post "force deleted" event.
     *
     * @param  Dir  $dir
     * @return void
     */
    public function forceDeleted(Dir $dir)
    {
        //
    }
}
