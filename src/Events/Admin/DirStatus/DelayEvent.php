<?php

namespace N1ebieski\IDir\Events\Admin\DirStatus;

use N1ebieski\IDir\Models\DirStatus;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class DelayEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * [public description]
     * @var DirStatus
     */
    public $dirStatus;

    /**
     * Undocumented function
     *
     * @param DirStatus $dirStatus
     */
    public function __construct(DirStatus $dirStatus)
    {
        $this->dirStatus = $dirStatus;
    }
}
