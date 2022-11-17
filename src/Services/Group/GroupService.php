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

namespace N1ebieski\IDir\Services\Group;

use Throwable;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\ValueObjects\Group\Slug;
use Illuminate\Support\Collection as Collect;
use Illuminate\Database\DatabaseManager as DB;
use N1ebieski\IDir\ValueObjects\Dir\Status as DirStatus;

class GroupService
{
    /**
     * Undocumented function
     *
     * @param Group $group
     * @param Collect $collect
     * @param DB $db
     */
    public function __construct(
        protected Group $group,
        protected Collect $collect,
        protected DB $db
    ) {
        //
    }

    /**
     *
     * @param array $attributes
     * @return Group
     * @throws Throwable
     */
    public function create(array $attributes): Group
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->group->fill($attributes);

            $this->group->save();

            if (array_key_exists('priv', $attributes)) {
                $this->group->privileges()->attach(array_filter($attributes['priv'] ?? []));
            }

            return $this->group;
        });
    }

    /**
     *
     * @param array $attributes
     * @return Group
     * @throws Throwable
     */
    public function update(array $attributes): Group
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->group->fill($attributes);

            if (array_key_exists('priv', $attributes)) {
                $this->group->privileges()->sync(array_filter($attributes['priv'] ?? []));
            }

            $this->group->save();

            return $this->group;
        });
    }

    /**
     *
     * @param int $position
     * @return bool
     * @throws Throwable
     */
    public function updatePosition(int $position): bool
    {
        return $this->db->transaction(function () use ($position) {
            return $this->group->update(['position' => $position]);
        });
    }

    /**
     * [delete description]
     * @return bool [description]
     */
    public function delete(): bool
    {
        return $this->db->transaction(function () {
            /** @var Group */
            $defaultGroup = $this->group->makeCache()->rememberBySlug(Slug::default());

            // If you delete a group, you have to change the alternative of other groups to Default
            $this->group->where('alt_id', $this->group->id)->update([
                'alt_id' => $defaultGroup->id
            ]);

            $this->group->dirs()->update([
                'group_id' => $defaultGroup->id,
                'privileged_at' => null,
                'privileged_to' => null
            ]);
            $this->group->dirs()->pending()->update(['status' => DirStatus::INACTIVE]);
            $this->group->dirs()->backlinkInactive()->update(['status' => DirStatus::INACTIVE]);

            // Manually remove relations, because the field model is polymorfic and foreign key doesn't work
            $this->group->fields()->detach();

            $this->group->delete();

            return true;
        });
    }
}
