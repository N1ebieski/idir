<?php

namespace N1ebieski\IDir\Events\Admin\Dir;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use N1ebieski\IDir\Models\Dir;

/**
 * [UpdateStatus description]
 */
class UpdateStatusEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * [public description]
     * @var Dir
     */
    public $dir;

    /**
     * [__construct description]
     * @param Dir $dir [description]
     */
    public function __construct(Dir $dir)
    {
        $this->dir = $dir;
    }
}
