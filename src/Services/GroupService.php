<?php

namespace N1ebieski\IDir\Services;

use N1ebieski\IDir\Models\Group;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as Collect;
use N1ebieski\ICore\Services\Interfaces\Creatable;
use N1ebieski\ICore\Services\Interfaces\Updatable;
use N1ebieski\ICore\Services\Interfaces\PositionUpdatable;
use N1ebieski\ICore\Services\Interfaces\Deletable;

/**
 * [GroupService description]
 */
class GroupService implements Creatable, Updatable, PositionUpdatable, Deletable
{
    /**
     * Model
     * @var Group
     */
    protected $group;

    /**
     * [private description]
     * @var Collect
     */
    protected $collect;

    /**
     * [__construct description]
     * @param Group     $group     [description]
     * @param Collect   $collect   [description]
     */
    public function __construct(Group $group, Collect $collect)
    {
        $this->group = $group;

        $this->collect = $collect;
    }

    /**
     * [create description]
     * @param  array $attributes [description]
     * @return Model             [description]
     */
    public function create(array $attributes) : Model
    {
        $this->group->fill(
            $this->collect->make($attributes)->except(['priv', 'prices'])->toArray()
        );
        $this->group->save();

        $this->group->privileges()->attach(array_filter($attributes['priv'] ?? []));

        $this->group->prices()->make()
            ->setRelations(['group' => $this->group])
            ->makeService()
            ->createOrUpdateGlobal(array_filter(
                (int)$attributes['payment'] === 1 ?
                    $this->collect->make($attributes['prices'])->flatten(1)->toArray()
                    : []
            ));

        return $this->group;
    }

    /**
     * [update description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function update(array $attributes) : bool
    {
        $this->group->fill(
            $this->collect->make($attributes)->except(['priv', 'prices'])->toArray()
        );
        $result = $this->group->save();

        $this->group->privileges()->sync(array_filter($attributes['priv'] ?? []));

        $this->group->prices()->make()
            ->setRelations(['group' => $this->group])
            ->makeService()
            ->organizeGlobal(array_filter(
                (int)$attributes['payment'] === 1 ?
                    $this->collect->make($attributes['prices'])->flatten(1)->toArray()
                    : []
            ));

        return $result;
    }

    /**
     * [updatePosition description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updatePosition(array $attributes) : bool
    {
        return $this->group->update(['position' => (int)$attributes['position']]);
    }

    /**
     * [delete description]
     * @return bool [description]
     */
    public function delete() : bool
    {
        // W przypadku usuwania grupy trzeba zmienić alternative innych grup na Default 1
        $this->group->where('alt_id', $this->group->id)->update(['alt_id' => 1]);

        $this->group->dirs()->update([
            'group_id' => 1,
            'privileged_at' => null,
            'privileged_to' => null
        ]);
        $this->group->dirs()->pending()->update(['status' => 0]);
        $this->group->dirs()->backlinkInactive()->update(['status' => 0]);

        // Manually remove relations, because the field model is polymorfic and foreign key doesn't work
        $this->group->fields()->detach();

        return $this->group->delete();
    }
}
