<?php

namespace N1ebieski\IDir\Services;

use N1ebieski\ICore\Services\Serviceable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as Collect;
use N1ebieski\IDir\Models\Dir;
use Illuminate\Contracts\Session\Session;
use N1ebieski\IDir\Models\Group;
use Carbon\Carbon;

/**
 * [DirService description]
 */
class DirService implements Serviceable
{
    /**
     * Model
     * @var Dir
     */
    private $dir;

    /**
     * [private description]
     * @var Group
     */
    private $group;

    /**
     * [private description]
     * @var Session
     */
    private $session;

    /**
     * [private description]
     * @var Collect
     */
    private $collect;

    /**
     * [__construct description]
     * @param Dir       $dir       [description]
     * @param Session   $session   [description]
     * @param Collect   $collect   [description]
     */
    public function __construct(Dir $dir, Session $session, Collect $collect)
    {
        $this->dir = $dir;
        $this->session = $session;
        $this->collect = $collect;
    }

    /**
     * @param Group $group
     *
     * @return static
     */
    public function setGroup(Group $group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * [createOrUpdateSession description]
     * @param  array $attributes [description]
     * @return void              [description]
     */
    public function createOrUpdateSession(array $attributes) : void
    {
        if ($this->session->has('dir')) {
            $this->updateSession($attributes);
        } else {
            $this->createSession($attributes);
        }
    }

    /**
     * [createSession description]
     * @param  array $attributes [description]
     * @return void              [description]
     */
    public function createSession(array $attributes) : void
    {
        $this->session->put('dir', $attributes);
    }

    /**
     * [updateSession description]
     * @param  array $attributes [description]
     * @return void              [description]
     */
    public function updateSession(array $attributes) : void
    {
        if (is_array($dir = $this->session->get('dir'))) {
            $this->session->put('dir', array_merge($dir, $attributes));
        }
    }

    /**
     * [makeStatus description]
     * @param  string|null  $payment_type  [description]
     * @return int [description]
     */
    protected function makeStatus(string $payment_type = null) : int
    {
        if ($payment_type === 'transfer') {
            return 2;
        }

        return $this->group->apply_status === 1 ? 1 : 0;
    }

    /**
     * [create description]
     * @param  array $attributes [description]
     * @return Model             [description]
     */
    public function create(array $attributes) : Model
    {
        $this->dir->fill($attributes);
        $this->dir->user()->associate(auth()->user());
        $this->dir->group()->associate($this->group);
        $this->dir->content = $this->dir->content_html;
        $this->dir->status = $this->makeStatus($attributes['payment_type'] ?? null);
        $this->dir->save();

        $this->dir->categories()->attach($attributes['categories']);

        $this->dir->tag($attributes['tags'] ?? []);

        return $this->dir;
    }

    /**
     * [update description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function update(array $attributes) : bool
    {

    }

    /**
     * Update Status attribute the specified Dir in storage.
     *
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updatePrivileged(array $attributes) : bool
    {
        return $this->dir->update([
            'privileged_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'privileged_to' => is_int($attributes['days']) ?
                $this->dir->privileged_to !== null ?
                    Carbon::parse($this->dir->privileged_to)->addDays($attributes['days'])
                    : Carbon::now()->addDays($attributes['days'])
                : null
        ]);
    }

    /**
     * [updateStatus description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updateStatus(array $attributes) : bool
    {

    }

    /**
     * [delete description]
     * @return bool [description]
     */
    public function delete() : bool
    {

    }

    /**
     * [deleteGlobal description]
     * @param  array $ids [description]
     * @return int        [description]
     */
    public function deleteGlobal(array $ids) : int
    {

    }
}
