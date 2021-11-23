<?php

namespace N1ebieski\IDir\Observers;

use N1ebieski\IDir\Models\Field\Field;

class FieldObserver
{
    /**
     * Handle the post "saving" event.
     *
     * @param  Field  $field
     * @return void
     */
    public function saving(Field $field)
    {
        $field->position = $field->position ?? $field->getNextAfterLastPosition();
    }

    /**
     * Handle the post "saved" event.
     *
     * @param  Field  $field
     * @return void
     */
    public function saved(Field $field)
    {
        // Everytime the model's position
        // is changed, all siblings reordering will happen,
        // so they will always keep the proper order.
        $field->reorderSiblings();
    }

    /**
     * Handle the post "created" event.
     *
     * @param  Field  $field
     * @return void
     */
    public function created(Field $field)
    {
        //
    }

    /**
     * Handle the post "updated" event.
     *
     * @param  Field  $field
     * @return void
     */
    public function updated(Field $field)
    {
        //
    }

    /**
     * Handle the post "deleted" event.
     *
     * @param  Field  $field
     * @return void
     */
    public function deleted(Field $field)
    {
        // Everytime the model is removed, we have to decrement siblings position by 1
        $field->decrementSiblings($field->position, null);
    }

    /**
     * Handle the post "restored" event.
     *
     * @param  Field  $field
     * @return void
     */
    public function restored(Field $field)
    {
        //
    }

    /**
     * Handle the post "force deleted" event.
     *
     * @param  Field  $field
     * @return void
     */
    public function forceDeleted(Field $field)
    {
        //
    }
}
