<?php

namespace N1ebieski\IDir\Services;

use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Group;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as Collect;
use Illuminate\Database\DatabaseManager as DB;
use N1ebieski\ICore\Services\Interfaces\Creatable;
use N1ebieski\ICore\Services\Interfaces\Deletable;
use N1ebieski\ICore\Services\Interfaces\Updatable;
use N1ebieski\ICore\Services\Interfaces\PositionUpdatable;

class GroupService implements Creatable, Updatable, PositionUpdatable, Deletable
{
    /**
     * Model
     * @var Group
     */
    protected $group;

    /**
     * Undocumented variable
     *
     * @var DB
     */
    protected $db;

    /**
     * [private description]
     * @var Collect
     */
    protected $collect;

    /**
     * Undocumented function
     *
     * @param Group $group
     * @param Collect $collect
     * @param DB $db
     */
    public function __construct(Group $group, Collect $collect, DB $db)
    {
        $this->group = $group;

        $this->collect = $collect;
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
            $this->group->fill(
                $this->collect->make($attributes)->except(['priv'])->toArray()
            );
            $this->group->save();

            $this->group->privileges()->attach(array_filter($attributes['priv'] ?? []));

            return $this->group;
        });
    }

    /**
     * [update description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function update(array $attributes) : bool
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->group->fill(
                $this->collect->make($attributes)->except(['priv'])->toArray()
            );

            $this->group->privileges()->sync(array_filter($attributes['priv'] ?? []));

            return $this->group->save();
        });
    }

    /**
     * [updatePosition description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updatePosition(array $attributes) : bool
    {
        return $this->db->transaction(function () use ($attributes) {
            return $this->group->update(['position' => (int)$attributes['position']]);
        });
    }

    /**
     * [delete description]
     * @return bool [description]
     */
    public function delete() : bool
    {
        return $this->db->transaction(function () {
            // If you delete a group, you have to change the alternative of other groups to Default
            $this->group->where('alt_id', $this->group->id)->update(['alt_id' => Group::DEFAULT]);

            $this->group->dirs()->update([
                'group_id' => Group::DEFAULT,
                'privileged_at' => null,
                'privileged_to' => null
            ]);
            $this->group->dirs()->pending()->update(['status' => Dir::INACTIVE]);
            $this->group->dirs()->backlinkInactive()->update(['status' => Dir::INACTIVE]);

            // Manually remove relations, because the field model is polymorfic and foreign key doesn't work
            $this->group->fields()->detach();

            return $this->group->delete();
        });
    }
}
