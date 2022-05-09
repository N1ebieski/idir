<?php

namespace N1ebieski\IDir\Services\Group;

use N1ebieski\IDir\Models\Group;
use Illuminate\Database\Eloquent\Model;
use N1ebieski\IDir\ValueObjects\Group\Slug;
use Illuminate\Support\Collection as Collect;
use Illuminate\Database\DatabaseManager as DB;
use N1ebieski\ICore\Services\Interfaces\CreateInterface;
use N1ebieski\ICore\Services\Interfaces\DeleteInterface;
use N1ebieski\ICore\Services\Interfaces\UpdateInterface;
use N1ebieski\IDir\ValueObjects\Dir\Status as DirStatus;
use N1ebieski\ICore\Services\Interfaces\PositionUpdateInterface;

/**
 *
 * @author Mariusz Wysokiński <kontakt@intelekt.net.pl>
 */
class GroupService implements
    CreateInterface,
    UpdateInterface,
    PositionUpdateInterface,
    DeleteInterface
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
    public function create(array $attributes): Model
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->group->fill(
                $this->collect->make($attributes)->except(['priv'])->toArray()
            );
            $this->group->save();

            if (array_key_exists('priv', $attributes)) {
                $this->group->privileges()->attach(array_filter($attributes['priv'] ?? []));
            }

            return $this->group;
        });
    }

    /**
     * [update description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function update(array $attributes): bool
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->group->fill(
                $this->collect->make($attributes)->except(['priv'])->toArray()
            );

            if (array_key_exists('priv', $attributes)) {
                $this->group->privileges()->sync(array_filter($attributes['priv'] ?? []));
            }

            return $this->group->save();
        });
    }

    /**
     * [updatePosition description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updatePosition(array $attributes): bool
    {
        return $this->db->transaction(function () use ($attributes) {
            return $this->group->update(['position' => (int)$attributes['position']]);
        });
    }

    /**
     * [delete description]
     * @return bool [description]
     */
    public function delete(): bool
    {
        return $this->db->transaction(function () {
            // If you delete a group, you have to change the alternative of other groups to Default
            $this->group->where('alt_id', $this->group->id)->update([
                'alt_id' => $this->group->makeCache()->rememberBySlug(Slug::default())->id
            ]);

            $this->group->dirs()->update([
                'group_id' => $this->group->makeCache()->rememberBySlug(Slug::default())->id,
                'privileged_at' => null,
                'privileged_to' => null
            ]);
            $this->group->dirs()->pending()->update(['status' => DirStatus::INACTIVE]);
            $this->group->dirs()->backlinkInactive()->update(['status' => DirStatus::INACTIVE]);

            // Manually remove relations, because the field model is polymorfic and foreign key doesn't work
            $this->group->fields()->detach();

            return $this->group->delete();
        });
    }
}
