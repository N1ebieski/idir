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

namespace N1ebieski\IDir\Services\DirBacklink;

use Throwable;
use Carbon\Carbon;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\DirBacklink;
use Illuminate\Database\DatabaseManager as DB;

class DirBacklinkService
{
    /**
     * Undocumented function
     *
     * @param DirBacklink $dirBacklink
     * @param Carbon $carbon
     * @param DB $db
     */
    public function __construct(
        protected DirBacklink $dirBacklink,
        protected Carbon $carbon,
        protected DB $db
    ) {
        //
    }

    /**
     *
     * @param array $attributes
     * @return DirBacklink
     * @throws Throwable
     */
    public function create(array $attributes): DirBacklink
    {
        return $this->db->transaction(function () use ($attributes) {
            if ($this->isBacklink($attributes)) {
                $this->dirBacklink->dir()->associate($attributes['dir']);
                $this->dirBacklink->link()->associate($attributes['backlink']);

                $this->dirBacklink->url = $attributes['backlink_url'];

                $this->dirBacklink->save();
            }

            return $this->dirBacklink;
        });
    }

    /**
     * [attemptNow description]
     * @return bool [description]
     */
    public function attemptedNow(): bool
    {
        return $this->db->transaction(function () {
            return $this->dirBacklink->update(['attempted_at' => Carbon::now()]);
        });
    }

    /**
     * [resetAttempts description]
     * @return bool [description]
     */
    public function resetAttempts(): bool
    {
        return $this->db->transaction(function () {
            return $this->dirBacklink->update(['attempts' => 0]);
        });
    }

    /**
     * [incrementAttempts description]
     * @return int [description]
     */
    public function incrementAttempts(): int
    {
        return $this->db->transaction(function () {
            return $this->dirBacklink->increment('attempts');
        });
    }

    /**
     *
     * @param array $attributes
     * @return null|DirBacklink
     * @throws Throwable
     */
    public function sync(array $attributes): ?DirBacklink
    {
        if (!$this->isBacklink($attributes)) {
            $this->clear($attributes['dir']);

            return null;
        }

        if (!$this->isSync($attributes)) {
            return null;
        }

        return $this->db->transaction(function () use ($attributes) {
            $this->clear($attributes['dir']);

            return $this->create($attributes);
        });
    }

    /**
     *
     * @param Dir $dir
     * @return int
     * @throws Throwable
     */
    public function clear(Dir $dir): int
    {
        return $this->db->transaction(function () use ($dir) {
            return $this->dirBacklink->where('dir_id', $dir->id)->delete();
        });
    }

    /**
     *
     * @param int $days
     * @return bool
     * @throws Throwable
     */
    public function delay(int $days): bool
    {
        return $this->db->transaction(function () use ($days) {
            return $this->dirBacklink->update([
                'attempts' => 0,
                'attempted_at' => $this->carbon->parse($this->dirBacklink->attempted_at)
                    ->addDays($days)
            ]);
        });
    }

    /**
     * Undocumented function
     *
     * @param array $attributes
     * @return boolean
     */
    protected function isBacklink(array $attributes): bool
    {
        return isset($attributes['backlink']) && isset($attributes['backlink_url']);
    }

    /**
     * Undocumented function
     *
     * @param array $attributes
     * @return boolean
     */
    protected function isSync(array $attributes): bool
    {
        return $this->dirBacklink->dir->backlink?->link_id !== (int)$attributes['backlink']
            // @phpstan-ignore-next-line
            || $this->dirBacklink->dir->backlink?->url !== $attributes['backlink_url'];
    }
}
