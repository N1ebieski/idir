<?php

namespace N1ebieski\IDir\Traits;

/**
 * [Positionable description]
 */
trait Positionable
{
    /**
     * [reorderSiblings description]
     * @return void [description]
     */
    public function reorderSiblings() : void
    {
        $originalPosition = $this->getOriginal('position');

        if (is_int($originalPosition)) {
            if ($this->position > $originalPosition) {
                $this->decrementSiblings($originalPosition, $this->position);
            }
            else if ($this->position < $originalPosition) {
                $this->incrementSiblings($this->position, $originalPosition);
            }
        } else {
            $this->incrementSiblings($this->position, null);
        }
    }

    /**
     * [decrementSiblings description]
     * @param  int|null $from [description]
     * @param  int|null $to   [description]
     * @return bool         [description]
     */
    public function decrementSiblings(int $from = null, int $to = null) : bool
    {
        return $this->siblings()
            ->when($from !== null, function($query) use ($from) {
                $query->where('position', '>', $from);
            })
            ->when($to !== null, function($query) use ($to) {
                $query->where('position', '<=', $to);
            })
            ->where('id', '<>', $this->id)
            ->decrement('position');
    }

    /**
     * [incrementSiblings description]
     * @param  int|null $from [description]
     * @param  int|null $to   [description]
     * @return bool         [description]
     */
    public function incrementSiblings(int $from = null, int $to = null) : bool
    {
        return $this->siblings()
            ->when($from !== null, function($query) use ($from) {
                $query->where('position', '>=', $from);
            })
            ->when($to !== null, function($query) use ($to) {
                $query->where('position', '<', $to);
            })
            ->where('id', '<>', $this->id)
            ->increment('position');
    }

    /**
     * [countSiblings description]
     * @return int [description]
     */
    public function countSiblings() : int
    {
        return $this->siblings()->count();
    }

    /**
     * [getNextAfterLastPosition description]
     * @return int [description]
     */
    public function getNextAfterLastPosition() : int
    {
        $last = $this->siblings()
            ->orderBy('position', 'desc')
            ->first('position');

        return is_int(optional($last)->position) ? $last->position + 1 : 0;
    }
}
