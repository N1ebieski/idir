<?php

namespace N1ebieski\IDir\Repositories\Privilege;

use N1ebieski\IDir\Models\Privilege;
use Illuminate\Database\Eloquent\Collection;

class PrivilegeRepo
{
    /**
     * [private description]
     * @var Privilege
     */
    protected $privilege;

    /**
     * [__construct description]
     * @param Privilege $privilege [description]
     */
    public function __construct(Privilege $privilege)
    {
        $this->privilege = $privilege;
    }

    /**
     * [getWithRole description]
     * @param  int        $id [description]
     * @return Collection     [description]
     */
    public function getWithGroup(int $id): Collection
    {
        return $this->privilege->with([
                'groups' => function ($query) use ($id) {
                    $query->where('id', $id);
                }
            ])
            ->orderBy('name', 'asc')
            ->get();
    }
}
