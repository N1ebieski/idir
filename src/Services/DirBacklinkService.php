<?php

namespace N1ebieski\IDir\Services;

use Carbon\Carbon;
use N1ebieski\IDir\Models\DirBacklink;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\DatabaseManager as DB;
use N1ebieski\ICore\Services\Interfaces\Creatable;

class DirBacklinkService implements Creatable
{
    /**
     * [private description]
     * @var DirBacklink
     */
    protected $dirBacklink;

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
     * @param DirBacklink $dirBacklink
     * @param Carbon $carbon
     * @param DB $db
     */
    public function __construct(DirBacklink $dirBacklink, Carbon $carbon, DB $db)
    {
        $this->dirBacklink = $dirBacklink;

        $this->carbon = $carbon;
        $this->db = $db;
    }

    /**
     * [create description]
     * @param  array $attributes [description]
     * @return Model             [description]
     */
    public function create(array $attributes) : Model
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->dirBacklink->dir()->associate($this->dirBacklink->dir);
            $this->dirBacklink->link()->associate($attributes['backlink']);
            $this->dirBacklink->url = $attributes['backlink_url'];
            $this->dirBacklink->save();

            return $this->dirBacklink;
        });
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

        return $this->db->transaction(function () use ($attributes) {
            $this->clear();

            return $this->create($attributes);
        });
    }

    /**
     * [clear description]
     * @return int [description]
     */
    public function clear() : int
    {
        return $this->db->transaction(function () {
            return $this->dirBacklink->where('dir_id', $this->dirBacklink->dir->id)->delete();
        });
    }

    /**
     * Undocumented function
     *
     * @param array $attributes
     * @return boolean
     */
    public function delay(array $attributes) : bool
    {
        return $this->db->transaction(function () use ($attributes) {
            return $this->dirBacklink->update([
                'attempts' => 0,
                'attempted_at' => $this->carbon->parse($this->dirBacklink->attempted_at)
                    ->addDays($attributes['delay'])
            ]);
        });
    }

    /**
     * Undocumented function
     *
     * @param array $attributes
     * @return boolean
     */
    protected function isSync(array $attributes) : bool
    {
        return isset($attributes['backlink'])
            && isset($attributes['backlink_url'])
            && (
                optional($this->dirBacklink->dir->backlink)->link_id !== (int)$attributes['backlink']
                || optional($this->dirBacklink->dir->backlink)->url !== $attributes['backlink_url']
            );
    }
}
