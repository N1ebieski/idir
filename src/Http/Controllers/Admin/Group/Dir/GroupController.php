<?php

namespace N1ebieski\IDir\Http\Controllers\Admin\Group\Dir;

use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use N1ebieski\IDir\Models\Group\Group;
use N1ebieski\IDir\Models\Group\Dir\Group as DirGroup;
use N1ebieski\IDir\Models\Privilege;
use N1ebieski\IDir\Http\Requests\Admin\Group\IndexRequest;
use N1ebieski\IDir\Http\Requests\Admin\Group\StoreRequest;
use N1ebieski\IDir\Http\Requests\Admin\Group\StoreGlobalRequest;
use N1ebieski\IDir\Http\Requests\Admin\Group\SearchRequest;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use N1ebieski\IDir\Http\Controllers\Admin\Group\Polymorphic;
use N1ebieski\IDir\Http\Controllers\Admin\Group\GroupController as GroupBaseController;

/**
 * [GroupController description]
 */
class GroupController extends GroupBaseController
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

    /**
     * Store collection of Categories with childrens in storage.
     *
     * @param  Group      $group      [description]
     * @param  StoreGlobalRequest  $request
     * @return JsonResponse
     */
    public function storeGlobal(Group $group, StoreGlobalRequest $request) : JsonResponse
    {
        $request->validate([
            'parent_id' => ['nullable', Rule::exists('categories', 'id')->where(function($query) {
                $query->where('model_type', $this->group->model_type);
            })],
        ]);

        return parent::storeGlobal($this->group, $request);
    }

    /**
     * Search Categories for specified name.
     *
     * @param  Group      $group [description]
     * @param  SearchRequest $request  [description]
     * @return JsonResponse                [description]
     */
    public function search(Group $group, SearchRequest $request) : JsonResponse
    {
        return parent::search($this->group, $request);
    }
}
