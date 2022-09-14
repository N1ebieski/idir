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

namespace N1ebieski\IDir\Events\Admin\DirBacklink;

use Illuminate\Queue\SerializesModels;
use N1ebieski\IDir\Models\DirBacklink;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use N1ebieski\IDir\Events\Interfaces\DirBacklink\DirBacklinkEventInterface;

class DelayEvent implements DirBacklinkEventInterface
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Undocumented function
     *
     * @param DirBacklink $dirBacklink
     */
    public function __construct(public DirBacklink $dirBacklink)
    {
        //
    }
}
