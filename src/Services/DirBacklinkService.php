<?php

namespace N1ebieski\IDir\Services;

use N1ebieski\ICore\Services\Serviceable;
use Illuminate\Database\Eloquent\Model;
use N1ebieski\IDir\Models\DirBacklink;

/**
 * [DirBacklinkService description]
 */
class DirBacklinkService implements Serviceable
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
     * [update description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function update(array $attributes) : bool
    {
        //
    }

    /**
     * [updateStatus description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updateStatus(array $attributes) : bool
    {
        //
    }

    /**
     * [delete description]
     * @return bool [description]
     */
    public function delete() : bool
    {
        //
    }

    /**
     * [deleteGlobal description]
     * @param  array $ids [description]
     * @return int        [description]
     */
    public function deleteGlobal(array $ids) : int
    {
        //
    }
}
