<?php

namespace N1ebieski\IDir\Services;

use N1ebieski\ICore\Services\Interfaces\Creatable;
use Illuminate\Database\Eloquent\Model;
use N1ebieski\IDir\Models\DirBacklink;

/**
 * [DirBacklinkService description]
 */
class DirBacklinkService implements Creatable
{
    /**
     * [private description]
     * @var DirBacklink
     */
    protected $dirBacklink;

    /**
     * @param DirBacklink $dirBacklink
     */
    public function __construct(DirBacklink $dirBacklink)
    {
        $this->dirBacklink = $dirBacklink;
    }

    /**
     * [create description]
     * @param  array $attributes [description]
     * @return Model             [description]
     */
    public function create(array $attributes) : Model
    {
        $this->dirBacklink->dir()->associate($this->dirBacklink->getDir());
        $this->dirBacklink->link()->associate($attributes['backlink']);
        $this->dirBacklink->url = $attributes['backlink_url'];
        $this->dirBacklink->save();

        return $this->dirBacklink;
    }

    /**
     * [sync description]
     * @param  array  $attributes [description]
     * @return Model|null             [description]
     */
    public function sync(array $attributes) : ?Model
    {
        $this->clear();

        if (isset($attributes['backlink_url'])) {
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
        return $this->dirBacklink->where('dir_id', $this->dirBacklink->getDir()->id)->delete();
    }
}
