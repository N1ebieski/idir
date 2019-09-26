<?php

namespace N1ebieski\IDir\Http\Controllers\Admin\Group;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use N1ebieski\IDir\Models\Group\Group;
use N1ebieski\IDir\Models\Privilege;
use N1ebieski\IDir\Http\Requests\Admin\Group\IndexRequest;
use N1ebieski\IDir\Http\Requests\Admin\Group\StoreRequest;

/**
 * [interface description]
 * @var [type]
 */
interface Polymorphic
{
    /**
     * Display a listing of the Group.
     *
     * @param  Group      $group      [description]
     * @param  IndexRequest  $request       [description]
     * @return View                         [description]
     */
    public function index(Group $group, IndexRequest $request) : View;

    /**
     * Show the form for creating a new Group.
     *
     * @param  Group      $group      [description]
     * @param  Privilege  $privilege  [description]
     * @return View
     */
    public function create(Group $group, Privilege $privilege) : View;

    /**
     * Store a newly created Group in storage.
     *
     * @param  Group      $group      [description]
     * @param  StoreRequest  $request
     * @return RedirectResponse
     */
    public function store(Group $group, StoreRequest $request) : RedirectResponse;
}
