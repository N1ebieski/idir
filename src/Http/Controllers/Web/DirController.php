<?php

namespace N1ebieski\IDir\Http\Controllers\Web;

use Illuminate\Http\RedirectResponse;

use Illuminate\View\View;
use N1ebieski\IDir\Http\Requests\Web\Dir\CreateFormRequest;
use N1ebieski\IDir\Http\Requests\Web\Dir\StoreFormRequest;
use N1ebieski\IDir\Http\Requests\Web\Dir\CreateSummaryRequest;
use N1ebieski\IDir\Http\Requests\Web\Dir\StoreSummaryRequest;
use N1ebieski\IDir\Models\Group\Dir\Group;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Category\Dir\Category;
use N1ebieski\IDir\Events\DirStore;

/**
 * [DirController description]
 */
class DirController
{
    /**
     * [createGroup description]
     * @param Group     $group     [description]
     * @return View
     */
    public function createGroup(Group $group) : View
    {
        return view('idir::web.dir.create.group', [
            'groups' => $group->getRepo()->getPublicWithPrivileges()
        ]);
    }

    /**
     * [createForm description]
     * @param  Group             $group   [description]
     * @param  CreateFormRequest $request [description]
     * @return View                       [description]
     */
    public function createForm(Group $group, CreateFormRequest $request) : View
    {
        return view('idir::web.dir.create.form', [
            'group' => $group,
            'max_tags' => config('idir.dir.max_tags'),
            'trumbowyg' => $group->privileges->contains('name', 'additional options for editing content')
                ? '_dir_trumbowyg' : null
        ]);
    }

    /**
     * [storeForm description]
     * @param  Group            $group   [description]
     * @param  Dir              $dir     [description]
     * @param  StoreFormRequest $request [description]
     * @return RedirectResponse          [description]
     */
    public function storeForm(Group $group, Dir $dir, StoreFormRequest $request) : RedirectResponse
    {
        $dir->getService()->createOrUpdateSession($request->validated());

        return redirect()->route('web.dir.create_summary', [$group->id]);
    }

    /**
     * [createSummary description]
     * @param  Group                $group    [description]
     * @param  Dir                  $dir      [description]
     * @param  Category             $category [description]
     * @param  CreateSummaryRequest $request  [description]
     * @return View                           [description]
     */
    public function createSummary(Group $group, Dir $dir, Category $category, CreateSummaryRequest $request) : View
    {
        $dir->getService()->createOrUpdateSession($request->validated());

        $categories = $category->getRepo()->getByIds(
            $request->session()->get('dir.categories')
        );

        return view('idir::web.dir.create.summary', compact('group', 'categories'));
    }

    /**
     * [storeSummary description]
     * @param  Group               $group   [description]
     * @param  Dir                 $dir     [description]
     * @param  StoreSummaryRequest $request [description]
     * @return RedirectResponse             [description]
     */
    public function storeSummary(Group $group, Dir $dir, StoreSummaryRequest $request) : RedirectResponse
    {
        $dir->getService()->setGroup($group)->create($request->validated());

        event(new DirStore($dir));

        return redirect()->route('web.dir.create_group')
            ->with('success', trans('idir::dirs.success.store'));
    }
}
