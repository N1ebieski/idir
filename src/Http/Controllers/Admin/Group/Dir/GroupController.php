<?php

namespace N1ebieski\IDir\Http\Controllers\Admin\Group\Dir;

use Illuminate\Http\RedirectResponse;
use N1ebieski\IDir\Models\Group\Group;
use N1ebieski\IDir\Models\Group\Dir\Group as DirGroup;
use N1ebieski\IDir\Models\Privilege;
use N1ebieski\IDir\Http\Requests\Admin\Group\IndexRequest;
use N1ebieski\IDir\Http\Requests\Admin\Group\StoreRequest;
use Illuminate\View\View;
use N1ebieski\IDir\Http\Controllers\Admin\Group\Polymorphic;
use N1ebieski\IDir\Http\Controllers\Admin\Group\GroupController as GroupBaseController;

/**
 * [GroupController description]
 */
class GroupController extends GroupBaseController implements Polymorphic
{
    /**
     * [__construct description]
     * @param DirGroup        $group        [description]
     */
    public function __construct(DirGroup $group)
    {
        parent::__construct($group);
    }

    /**
     * Display a listing of the Group.
     *
     * @param  Group      $group      [description]
     * @param  IndexRequest  $request       [description]
     * @return View                         [description]
     */
    public function index(Group $group, IndexRequest $request) : View
    {
        return parent::index($this->group, $request);
    }

    /**
     * Show the form for creating a new Group.
     *
     * @param  Group      $group      [description]
     * @param  Privilege  $privilege  [description]
     * @return View
     */
    public function create(Group $group, Privilege $privilege) : View
    {
        return parent::create($this->group, $privilege);
    }

    /**
     * Store a newly created Group in storage.
     *
     * @param  Group      $group      [description]
     * @param  StoreRequest  $request
     * @return RedirectResponse
     */
    public function store(Group $group, StoreRequest $request) : RedirectResponse
    {
        return parent::store($this->group, $request);
    }
}
