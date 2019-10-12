<?php

namespace N1ebieski\IDir\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use N1ebieski\IDir\Models\Dir;

/**
 * [DirStore description]
 */
class DirStore
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * [public description]
     * @var Dir
     */
    public $dir;

    /**
     * Create a new event instance.
     *
     * @param Dir         $dir    [description]
     * @return void
     */
    public function __construct(Dir $dir)
    {
        $this->dir = $dir;
    }
}
