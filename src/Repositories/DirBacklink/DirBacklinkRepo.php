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

namespace N1ebieski\IDir\Repositories\DirBacklink;

use Closure;
use Carbon\Carbon;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\DirBacklink;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\IDir\ValueObjects\Dir\Status;
use Illuminate\Contracts\Config\Repository as Config;

class DirBacklinkRepo
{
    /**
     * [__construct description]
     * @param DirBacklink $dirBacklink [description]
     * @param Config   $config   [description]
     */
    public function __construct(
        protected DirBacklink $dirBacklink,
        protected Config $config
    ) {
        //
    }

    /**
     * Undocumented function
     *
     * @param Closure $closure
     * @param string $timestamp
     * @return bool
     */
    public function chunkAvailableHasBacklinkRequirementByAttemptedAt(
        Closure $closure,
        string $timestamp
    ): bool {
        return $this->dirBacklink->newQuery()
            ->whereHas('dir', function (Builder|Dir $query) {
                return $query->whereIn('status', [Status::ACTIVE, Status::BACKLINK_INACTIVE])
                    ->whereHas('group', function (Builder|Group $query) {
                        return $query->obligatoryBacklink();
                    });
            })
            ->where(function (Builder $query) use ($timestamp) {
                return $query->whereDate(
                    'attempted_at',
                    '<',
                    Carbon::parse($timestamp)->format('Y-m-d')
                )
                ->orWhere(function (Builder $query) use ($timestamp) {
                    return $query->whereDate(
                        'attempted_at',
                        '=',
                        Carbon::parse($timestamp)->format('Y-m-d')
                    )
                    ->whereTime(
                        'attempted_at',
                        '<=',
                        Carbon::parse($timestamp)->format('H:i:s')
                    );
                })
                ->orWhere('attempted_at', null);
            })
            ->chunk(1000, $closure);
    }
}
