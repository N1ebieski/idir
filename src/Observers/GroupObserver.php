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

use N1ebieski\IDir\Models\Group;

class GroupObserver
{
    /**
     * Handle the group "saving" event.
     *
     * @param  Group  $group
     * @return void
     */
    public function saving(Group $group)
    {
        $group->position = $group->position ?? $group->getNextAfterLastPosition();
    }

    /**
     * Handle the group "saved" event.
     *
     * @param  Group  $group
     * @return void
     */
    public function saved(Group $group)
    {
        // Everytime the model's position
        // is changed, all siblings reordering will happen,
        // so they will always keep the proper order.
        $group->reorderSiblings();
    }

    /**
     * Handle the group "created" event.
     *
     * @param  Group  $group
     * @return void
     */
    public function created(Group $group)
    {
        //
    }

    /**
     * Handle the group "updated" event.
     *
     * @param  Group  $group
     * @return void
     */
    public function updated(Group $group)
    {
        //
    }

    /**
     * Handle the group "deleted" event.
     *
     * @param  Group  $group
     * @return void
     */
    public function deleted(Group $group)
    {
        // Everytime the model is removed, we have to decrement siblings position by 1
        $group->decrementSiblings($group->position, null);
    }

    /**
     * Handle the group "restored" event.
     *
     * @param  Group  $group
     * @return void
     */
    public function restored(Group $group)
    {
        //
    }

    /**
     * Handle the group "force deleted" event.
     *
     * @param  Group  $group
     * @return void
     */
    public function forceDeleted(Group $group)
    {
        //
    }
}
