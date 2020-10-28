<?php

namespace N1ebieski\IDir\Events\Admin\DirBacklink;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use N1ebieski\IDir\Models\DirBacklink;

class DelayEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * [public description]
     * @var DirBacklink
     */
    public $dirBacklink;

    /**
     * Undocumented function
     *
     * @param DirBacklink $dirBacklink
     */
    public function __construct(DirBacklink $dirBacklink)
    {
        $this->dirBacklink = $dirBacklink;
    }
}
