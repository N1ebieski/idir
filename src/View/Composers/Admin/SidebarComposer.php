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

use InvalidArgumentException;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\ValueObjects\Dir\Status;
use N1ebieski\ICore\View\Composers\Composer;
use Illuminate\Database\ClassMorphViolationException;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Contracts\Container\BindingResolutionException;

class SidebarComposer extends Composer
{
    /**
     * Undocumented function
     *
     * @param Dir $dir
     */
    public function __construct(protected Dir $dir)
    {
        //
    }

    /**
     *
     * @return int
     * @throws BindingResolutionException
     */
    public function dirsInactiveCount(): int
    {
        $inactive = $this->dir->makeRepo()
            ->countByStatus()
            ->filter(fn (Dir $dir) => $dir->status->isEquals(Status::inactive()))
            ->first();

        return $inactive?->count ?? 0;
    }

    /**
     *
     * @return int
     * @throws BindingResolutionException
     * @throws MassAssignmentException
     * @throws ClassMorphViolationException
     * @throws InvalidArgumentException
     */
    public function dirsReportedCount(): int
    {
        return $this->dir->makeRepo()->countReported();
    }
}
