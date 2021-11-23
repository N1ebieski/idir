<?php

namespace N1ebieski\IDir\Events\Admin\DirBacklink;

use Illuminate\Queue\SerializesModels;
use N1ebieski\IDir\Models\DirBacklink;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class DelayEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

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
