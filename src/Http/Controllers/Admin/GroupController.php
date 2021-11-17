<?php

namespace N1ebieski\IDir\Http\Controllers\Admin;

use N1ebieski\IDir\Models\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Filters\Admin\Group\IndexFilter;
use N1ebieski\IDir\Http\Requests\Admin\Group\EditRequest;
use N1ebieski\IDir\Http\Requests\Admin\Group\IndexRequest;
use N1ebieski\IDir\Http\Requests\Admin\Group\StoreRequest;
use N1ebieski\IDir\Http\Requests\Admin\Group\UpdateRequest;
use N1ebieski\IDir\Http\Requests\Admin\Group\DestroyRequest;
use N1ebieski\IDir\View\ViewModels\Admin\Group\EditViewModel;
use N1ebieski\IDir\View\ViewModels\Admin\Group\CreateViewModel;
use N1ebieski\IDir\Http\Requests\Admin\Group\UpdatePositionRequest;

class GroupController
{
    /**
     * Display a listing of the Group.
     *
     * @param  Group      $group      [description]
     * @param  IndexRequest  $request       [description]
     * @param  IndexFilter   $filter        [description]
     * @return HttpResponse                         [description]
     */
    public function index(Group $group, IndexRequest $request, IndexFilter $filter): HttpResponse
    {
        return Response::view('idir::admin.group.index', [
            'groups' => $group->makeRepo()->paginateByFilter($filter->all()),
            'filter' => $filter->all(),
            'paginate' => Config::get('database.paginate')
        ]);
    }

    /**
     * Show the form for creating a new Group.
     *
     * @return HttpResponse
     */
    public function create(): HttpResponse
    {
        return Response::view('idir::admin.group.create', App::make(CreateViewModel::class));
    }

    /**
     * Store a newly created Group in storage.
     *
     * @param  Group      $group      [description]
     * @param  StoreRequest  $request
     * @return RedirectResponse
     */
    public function store(Group $group, StoreRequest $request): RedirectResponse
    {
        $group->makeService()->create($request->all());

        return Response::redirectToRoute("admin.group.index")
            ->with('success', Lang::get('idir::groups.success.store'));
    }

    /**
     * Show the form for editing the specified Group.
     *
     * @param  Group       $group
     * @param  EditRequest $request    [description]
     * @return HttpResponse
     */
    public function edit(Group $group, EditRequest $request): HttpResponse
    {
        return Response::view(
            'idir::admin.group.edit',
            App::make(EditViewModel::class, [
                'group' => $group
            ])
        );
    }

    /**
     * Update the specified Group in storage.
     *
     * @param  Group      $group [description]
     * @param  UpdateRequest $request  [description]
     * @return RedirectResponse
     */
    public function update(Group $group, UpdateRequest $request): RedirectResponse
    {
        $group->makeService()->update($request->all());

        return Response::redirectToRoute('admin.group.edit', [$group->id])
            ->with('success', Lang::get('idir::groups.success.update'));
    }

    /**
     * [editPosition description]
     * @param  Group     $group [description]
     * @return JsonResponse           [description]
     */
    public function editPosition(Group $group): JsonResponse
    {
        return Response::json([
            'success' => '',
            'view' => View::make('idir::admin.group.edit_position', [
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
    public function updatePosition(Group $group, UpdatePositionRequest $request): JsonResponse
    {
        $group->makeService()->updatePosition($request->only('position'));

        return Response::json([
            'success' => '',
            'siblings' => $group->makeRepo()->getSiblingsAsArray()
        ]);
    }

    /**
     * Remove the specified Group from storage.
     *
     * @param  Group $group
     * @param  DestroyRequest $request
     * @return RedirectResponse
     */
    public function destroy(Group $group, DestroyRequest $request): RedirectResponse
    {
        $group->makeService()->delete();

        return Response::redirectToRoute("admin.group.index")
            ->with('success', Lang::get('idir::groups.success.destroy'));
    }
}
