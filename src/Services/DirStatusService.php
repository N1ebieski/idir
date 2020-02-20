<?php

namespace N1ebieski\IDir\Services;

use N1ebieski\ICore\Services\Interfaces\Creatable;
use Illuminate\Database\Eloquent\Model;
use N1ebieski\IDir\Models\DirStatus;

/**
 * [DirStatusService description]
 */
class DirStatusService implements Creatable
{
    /**
     * [private description]
     * @var DirStatus
     */
    protected $dirStatus;

    /**
     * @param DirStatus $dirStatus
     */
    public function __construct(DirStatus $dirStatus)
    {
        $this->dirStatus = $dirStatus;
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
        $this->clear();

        if (isset($attributes['url'])) {
            return $this->create($attributes);
        }

        return null;
    }

    /**
     * [clear description]
     * @return int [description]
     */
    public function clear() : int
    {
        return $this->dirStatus->where('dir_id', $this->dirStatus->getDir()->id)->delete();
    }
}
