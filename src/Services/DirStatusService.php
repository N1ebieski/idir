<?php

namespace N1ebieski\IDir\Services;

use Carbon\Carbon;
use N1ebieski\ICore\Services\Interfaces\Creatable;
use Illuminate\Database\Eloquent\Model;
use N1ebieski\IDir\Models\DirStatus;

class DirStatusService implements Creatable
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
     * Undocumented function
     *
     * @param DirStatus $dirStatus
     * @param Carbon $carbon
     */
    public function __construct(DirStatus $dirStatus, Carbon $carbon)
    {
        $this->dirStatus = $dirStatus;

        $this->carbon = $carbon;
    }

    /**
     * Undocumented function
     *
     * @param array $attributes
     * @return boolean
     */
    protected function isSync(array $attributes) : bool
    {
        return isset($attributes['url'])
            && $this->dirStatus->getDir()->url !== $attributes['url'];
    }

    /**
     * [create description]
     * @param  array $attributes [description]
     * @return Model             [description]
     */
    public function create(array $attributes) : Model
    {
        $this->dirStatus->dir()->associate($this->dirStatus->getDir());
        $this->dirStatus->save();

        return $this->dirStatus;
    }

    /**
     * [sync description]
     * @param  array  $attributes [description]
     * @return Model|null             [description]
     */
    public function sync(array $attributes) : ?Model
    {
        if (!$this->isSync($attributes)) {
            return null;
        }

        $this->clear();

        return $this->create($attributes);
    }

    /**
     * [clear description]
     * @return int [description]
     */
    public function clear() : int
    {
        return $this->dirStatus->where('dir_id', $this->dirStatus->getDir()->id)->delete();
    }

    /**
     * Undocumented function
     *
     * @param array $attributes
     * @return boolean
     */
    public function delay(array $attributes) : bool
    {
        return $this->dirStatus->update([
            'attempts' => 0,
            'attempted_at' => $this->carbon->parse($this->dirStatus->attempted_at)
                ->addDays($attributes['delay'])
        ]);
    }
}
