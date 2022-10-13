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

namespace N1ebieski\IDir\Events\Web\Tag\Dir;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use N1ebieski\IDir\Events\Interfaces\Dir\DirCollectionEventInterface;

class ShowEvent implements DirCollectionEventInterface
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     *
     * @param LengthAwarePaginator $dirs
     * @return void
     */
    public function __construct(public LengthAwarePaginator $dirs)
    {
        //
    }
}
