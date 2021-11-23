<?php

namespace N1ebieski\IDir\Events\Web\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class DestroyEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * [public description]
     * @var Dir
     */
    public $dir;

    /**
     * [public description]
     * @var string|null
     */
    public $reason;

    /**
     * [__construct description]
     * @param Dir    $dir    [description]
     * @param string|null $reason [description]
     */
    public function __construct(Dir $dir, string $reason = null)
    {
        $this->dir = $dir;

        $this->reason = $reason;
    }
}
