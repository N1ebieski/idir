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

namespace N1ebieski\IDir\Services\DirStatus;

use Throwable;
use Carbon\Carbon;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\DirStatus;
use Illuminate\Database\DatabaseManager as DB;

class DirStatusService
{
    /**
     * Undocumented function
     *
     * @param DirStatus $dirStatus
     * @param Carbon $carbon
     * @param DB $db
     */
    public function __construct(
        protected DirStatus $dirStatus,
        protected Carbon $carbon,
        protected DB $db
    ) {
        //
    }

    /**
     *
     * @param array $attributes
     * @return DirStatus
     * @throws Throwable
     */
    public function create(array $attributes): DirStatus
    {
        return $this->db->transaction(function () use ($attributes) {
            if ($this->isUrl($attributes)) {
                $this->dirStatus->dir()->associate($attributes['dir']);

                $this->dirStatus->save();
            }

            return $this->dirStatus;
        });
    }

    /**
     * [attemptNow description]
     * @return bool [description]
     */
    public function attemptedNow(): bool
    {
        return $this->db->transaction(function () {
            return $this->dirStatus->update(['attempted_at' => Carbon::now()]);
        });
    }

    /**
     * [resetAttempts description]
     * @return bool [description]
     */
    public function resetAttempts(): bool
    {
        return $this->db->transaction(function () {
            return $this->dirStatus->update(['attempts' => 0]);
        });
    }

    /**
     * [incrementAttempts description]
     * @return int [description]
     */
    public function incrementAttempts(): int
    {
        return $this->db->transaction(function () {
            return $this->dirStatus->increment('attempts');
        });
    }

    /**
     *
     * @param array $attributes
     * @return null|DirStatus
     * @throws Throwable
     */
    public function sync(array $attributes): ?DirStatus
    {
        if (!$this->isUrl($attributes)) {
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
            return $this->dirStatus->where('dir_id', $dir->id)->delete();
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
            return $this->dirStatus->update([
                'attempts' => 0,
                'attempted_at' => $this->carbon->parse($this->dirStatus->attempted_at)
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
    protected function isUrl(array $attributes): bool
    {
        return isset($attributes['url']);
    }

    /**
     * Undocumented function
     *
     * @param array $attributes
     * @return boolean
     */
    protected function isSync(array $attributes): bool
    {
        return $this->dirStatus->dir->url->getValue() !== $attributes['url'];
    }
}
