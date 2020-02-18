<?php

namespace N1ebieski\IDir\Observers;

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
        $dir->status()->create();

        cache()->tags(['dirs', 'links'])->flush();
    }

    /**
     * Handle the post "updated" event.
     *
     * @param  Dir  $dir
     * @return void
     */
    public function updated(Dir $dir)
    {
        cache()->tags(['dir.'.$dir->slug, 'dirs', 'links'])->flush();
    }

    /**
     * Handle the post "deleted" event.
     *
     * @param  Dir  $dir
     * @return void
     */
    public function deleted(Dir $dir)
    {
        cache()->tags(['dir.'.$dir->slug, 'dirs', 'links'])->flush();
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
