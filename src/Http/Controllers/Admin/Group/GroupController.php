<?php

namespace N1ebieski\IDir\Http\Controllers\Admin\Group;

use N1ebieski\IDir\Models\Group\Group;
use N1ebieski\IDir\Models\Privilege;
use N1ebieski\IDir\Http\Requests\Admin\Group\IndexRequest;
use N1ebieski\IDir\Http\Requests\Admin\Group\UpdateStatusRequest;
use N1ebieski\IDir\Http\Requests\Admin\Group\UpdatePositionRequest;
use N1ebieski\IDir\Http\Requests\Admin\Group\UpdateRequest;
use N1ebieski\IDir\Http\Requests\Admin\Group\StoreRequest;
use N1ebieski\IDir\Http\Requests\Admin\Group\StoreGlobalRequest;
use N1ebieski\IDir\Http\Requests\Admin\Group\SearchRequest;
use N1ebieski\IDir\Http\Requests\Admin\Group\DestroyGlobalRequest;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use N1ebieski\IDir\Http\Controllers\Admin\Group\Polymorphic;

/**
 * Base Group Controller
 */
class GroupController
{
    /**
     * Model. Must be protected!
     * @var Group
     */
    protected $group;

    /**
     * [__construct description]
     * @param Group        $group        [description]
     */
    public function __construct(Group $group)
    {
        $this->group = $group;
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
        return view('idir::admin.group.index', [
            'model' => $group,
            'groups' => $group->getRepo()->paginate()
        ]);
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
        return response()->json([
            'success' => '',
            'view' => view('icore::admin.group.partials.search', [
                'categories' => $group->getRepo()->getBySearch($request->get('name')),
                'checked' => false
            ])->render()
        ]);
    }

    /**
     * Show the form for creating a new Group.
     *
     * @param  Group      $group      [description]
     * @param Privilege   $privilege  [description]
     * @return View
     */
    public function create(Group $group, Privilege $privilege) : View
    {
        return view('idir::admin.group.create', [
            'model' => $group,
            'privileges' => $privilege->orderBy('name', 'asc')->get()
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

        return redirect()->route("admin.group.{$group->poli}.index")
            ->with('success', trans('idir::groups.success.store') );
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
        $group->getService()->createGlobal($request->only(['names', 'parent_id', 'clear']));

        $request->session()->flash('success', trans('icore::categories.success.store_global'));

        return response()->json(['success' => '' ]);
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
     * @param  Group $group
     * @return JsonResponse
     */
    public function edit(Group $group) : JsonResponse
    {
        return response()->json([
            'success' => '',
            'view' => view('icore::admin.group.edit', [
                'group' => $group,
                'categories' => $group->getService()->getAsFlatTreeExceptSelf()
            ])->render()
        ]);
    }

    /**
     * Update the specified Group in storage.
     *
     * @param  Group      $group [description]
     * @param  UpdateRequest $request  [description]
     * @return JsonResponse                [description]
     */
    public function update(Group $group, UpdateRequest $request) : JsonResponse
    {
        $group->getService()->update($request->only(['parent_id', 'icon', 'name']));

        return response()->json([
            'success' => '',
            'view' => view('icore::admin.group.group', [
                // Niezbyt ładny hook, ale trzeba na nowo pobrać ancestory
                'group' => $group->resolveRouteBinding($group->id),
                'show_ancestors' => true
            ])->render()
        ]);
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
                'group' => $group->loadCount('siblings')
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
     * Update Status attribute the specified Comment in storage.
     *
     * @param  Group            $group [description]
     * @param  UpdateStatusRequest $request  [description]
     * @return JsonResponse                        [description]
     */
    public function updateStatus(Group $group, UpdateStatusRequest $request) : JsonResponse
    {
        $group->getService()->updateStatus($request->only('status'));

        $groupRepo = $group->getRepo();

        return response()->json([
            'success' => '',
            'status' => $group->status,
            // Na potrzebę jQuery pobieramy potomków i przodków, żeby na froncie
            // zaznaczyć odpowiednie rowsy jako aktywowane bądź nieaktywne
            'ancestors' => $groupRepo->getAncestorsAsArray(),
            'descendants' => $groupRepo->getDescendantsAsArray(),
        ]);
    }

    /**
     * Remove the specified Group from storage.
     *
     * @param  Group $group
     * @return JsonResponse
     */
    public function destroy(Group $group) : JsonResponse
    {
        // Pobieramy potomków aby na froncie jQuery wiedział jakie rowsy usunąć
        $descendants = $group->getRepo()->getDescendantsAsArray();

        $group->getService()->delete();

        return response()->json([
            'success' => '',
            'descendants' => $descendants,
        ]);
    }

    /**
     * Remove the collection of Categories from storage.
     *
     * @param  Group             $group [description]
     * @param  DestroyGlobalRequest $request  [description]
     * @return RedirectResponse               [description]
     */
    public function destroyGlobal(Group $group, DestroyGlobalRequest $request) : RedirectResponse
    {
        $deleted = $group->getService()->deleteGlobal($request->get('select'));
        //$deleted = $group->whereIn('id', $request->get('select'))->delete();

        return redirect()->back()->with('success', trans('icore::categories.success.destroy_global', ['affected' => $deleted]));
    }
}
