<?php

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
