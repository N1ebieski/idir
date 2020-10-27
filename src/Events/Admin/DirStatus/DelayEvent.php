<?php

namespace N1ebieski\IDir\Events\Admin\DirStatus;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use N1ebieski\IDir\Models\DirStatus;

class DelayEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

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
