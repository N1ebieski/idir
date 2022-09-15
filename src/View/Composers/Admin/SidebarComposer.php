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

namespace N1ebieski\IDir\View\Composers\Admin;

use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\ValueObjects\Dir\Status;
use N1ebieski\ICore\View\Composers\Composer;

class SidebarComposer extends Composer
{
    /**
     * Undocumented variable
     *
     * @var int
     */
    public $dirs_inactive_count;

    /**
     * Undocumented variable
     *
     * @var int
     */
    public $dirs_reported_count;

    /**
     * Undocumented function
     *
     * @param Dir $dir
     */
    public function __construct(Dir $dir)
    {
        $this->dirs_inactive_count = $dir->makeRepo()->countByStatus()
            ->firstWhere('status', Status::inactive())->count ?? 0;

        $this->dirs_reported_count = $dir->makeRepo()->countReported();
    }
}
