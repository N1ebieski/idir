<?php

namespace N1ebieski\IDir\Services\DirStatus;

use Throwable;
use Carbon\Carbon;
use N1ebieski\IDir\Models\DirStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\DatabaseManager as DB;
use N1ebieski\ICore\Services\Interfaces\CreateInterface;

/**
 *
 * @author Mariusz Wysokiński <kontakt@intelekt.net.pl>
 */
class DirStatusService implements CreateInterface
{
    /**
     * [private description]
     * @var DirStatus
     */
    protected $dirStatus;

    /**
     * Undocumented variable
     *
     * @var Carbon
     */
    protected $carbon;

    /**
     * Undocumented variable
     *
     * @var DB
     */
    protected $db;

    /**
     * Undocumented function
     *
     * @param DirStatus $dirStatus
     * @param Carbon $carbon
     * @param DB $db
     */
    public function __construct(DirStatus $dirStatus, Carbon $carbon, DB $db)
    {
        $this->dirStatus = $dirStatus;

        $this->carbon = $carbon;
        $this->db = $db;
    }

    /**
     * [create description]
     * @param  array $attributes [description]
     * @return Model             [description]
     */
    public function create(array $attributes): Model
    {
        return $this->db->transaction(function () use ($attributes) {
            if ($this->isUrl($attributes)) {
                $this->dirStatus->dir()->associate($this->dirStatus->dir);
                $this->dirStatus->save();
            }

            return $this->dirStatus;
        });
    }

    /**
     * [sync description]
     * @param  array  $attributes [description]
     * @return Model|null             [description]
     */
    public function sync(array $attributes): ?Model
    {
        if (!$this->isUrl($attributes)) {
            $this->clear();

            return null;
        }

        if (!$this->isSync($attributes)) {
            return null;
        }

        return $this->db->transaction(function () use ($attributes) {
            $this->clear();

            return $this->create($attributes);
        });
    }

    /**
     * [clear description]
     * @return int [description]
     */
    public function clear(): int
    {
        return $this->db->transaction(function () {
            return $this->dirStatus->where('dir_id', $this->dirStatus->dir->id)->delete();
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
