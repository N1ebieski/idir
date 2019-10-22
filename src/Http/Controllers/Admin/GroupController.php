<?php

namespace N1ebieski\IDir\Http\Controllers\Admin;

use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Privilege;
use N1ebieski\IDir\Http\Requests\Admin\Group\IndexRequest;
use N1ebieski\IDir\Http\Requests\Admin\Group\UpdatePositionRequest;
use N1ebieski\IDir\Http\Requests\Admin\Group\UpdateRequest;
use N1ebieski\IDir\Http\Requests\Admin\Group\StoreRequest;
use N1ebieski\IDir\Http\Requests\Admin\Group\CreateRequest;
use N1ebieski\IDir\Http\Requests\Admin\Group\EditRequest;
use N1ebieski\IDir\Filters\Admin\Group\IndexFilter;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

/**
 * Base Group Controller
 */
class GroupController
{
    /**
     * Display a listing of the Group.
     *
     * @param  Group      $group      [description]
     * @param  IndexRequest  $request       [description]
     * @param  IndexFilter   $filter        [description]
     * @return View                         [description]
     */
    public function index(Group $group, IndexRequest $request, IndexFilter $filter) : View
    {
        return view('idir::admin.group.index', [
            'groups' => $group->getRepo()->paginateByFilter($filter->all()),
            'filter' => $filter->all(),
            'paginate' => config('database.paginate')
        ]);
    }

    /**
     * Show the form for creating a new Group.
     *
     * @param  Group      $group      [description]
     * @param Privilege   $privilege  [description]
     * @param CreateRequest $request  [description]
     * @return View
     */
    public function create(Group $group, Privilege $privilege, CreateRequest $request) : View
    {
        return view('idir::admin.group.create', [
            'privileges' => $privilege->orderBy('name', 'asc')->get(),
            'groups' => $group->orderBy('id', 'asc')->get()
        ]);
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
        $group->getService()->create($request->all());

        return redirect()->route("admin.group.index")
            ->with('success', trans('idir::groups.success.store') );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified Group.
     *
     * @param  Group       $group
     * @param  Privilege   $privilege  [description]
     * @param  EditRequest $request    [description]
     * @return View
     */
    public function edit(Group $group, Privilege $privilege, EditRequest $request) : View
    {
        return view('idir::admin.group.edit', [
            'group' => $group,
            'groups' => $group->orderBy('id', 'asc')->get(),
            'privileges' => $privilege->getRepo()->getWithGroup($group->id)
        ]);
    }

    /**
     * Update the specified Group in storage.
     *
     * @param  Group      $group [description]
     * @param  UpdateRequest $request  [description]
     * @return RedirectResponse
     */
    public function update(Group $group, UpdateRequest $request) : RedirectResponse
    {
        $group->getService()->update($request->all());

        return redirect()->route('admin.group.edit', [$group->id])
            ->with('success', trans('idir::groups.success.update') );
    }

    /**
     * [editPosition description]
     * @param  Group     $group [description]
     * @return JsonResponse           [description]
     */
    public function editPosition(Group $group) : JsonResponse
    {
        return response()->json([
            'success' => '',
            'view' => view('idir::admin.group.edit_position', [
                'group' => $group,
                'siblings_count' => $group->count()
            ])->render()
        ]);
    }

    /**
     * [updatePosition description]
     * @param  Group              $group [description]
     * @param  UpdatePositionRequest $request  [description]
     * @return JsonResponse                    [description]
     */
    public function updatePosition(Group $group, UpdatePositionRequest $request) : JsonResponse
    {
        $group->getService()->updatePosition($request->only('position'));

        return response()->json([
            'success' => '',
            'siblings' => $group->getRepo()->getSiblingsAsArray()
        ]);
    }

    /**
     * Remove the specified Group from storage.
     *
     * @param  Group $group
     * @return RedirectResponse
     */
    public function destroy(Group $group) : RedirectResponse
    {
        $group->getService()->delete();

        return redirect()->route("admin.group.index")
            ->with('success', trans('idir::groups.success.destroy'));
    }
}
