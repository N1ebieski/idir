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

namespace N1ebieski\IDir\Repositories\DirStatus;

use Closure;
use Carbon\Carbon;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\DirStatus;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\IDir\ValueObjects\Dir\Status;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DirStatusRepo
{
    /**
     * [__construct description]
     * @param DirStatus $dirStatus [description]
     * @param Config   $config   [description]
     */
    public function __construct(
        protected DirStatus $dirStatus,
        protected Config $config
    ) {
        //
    }

    /**
     * Undocumented function
     *
     * @param Closure $callback
     * @param string $timestamp
     * @return boolean
     */
    public function chunkAvailableHasUrlByAttemptedAt(Closure $callback, string $timestamp): bool
    {
        return $this->dirStatus->newQuery()
            ->whereHas('dir', function (BelongsTo|Builder|Dir $query) {
                return $query->whereIn('status', [Status::ACTIVE, Status::STATUS_INACTIVE])
                    ->whereNotNull('url');
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
            ->orderBy('attempted_at', 'asc')
            ->chunk(1000, $callback);
    }
}
