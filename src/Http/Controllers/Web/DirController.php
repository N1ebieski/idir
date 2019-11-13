<?php

namespace N1ebieski\IDir\Http\Controllers\Web;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use N1ebieski\IDir\Http\Requests\Web\Dir\CreateFormRequest;
use N1ebieski\IDir\Http\Requests\Web\Dir\StoreFormRequest;
use N1ebieski\IDir\Http\Requests\Web\Dir\PaymentCodeRequest;
use N1ebieski\IDir\Http\Requests\Web\Dir\CreateSummaryRequest;
use N1ebieski\IDir\Http\Requests\Web\Dir\StoreSummaryRequest;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\ICore\Models\Link;
use N1ebieski\IDir\Models\Category\Dir\Category;
use N1ebieski\IDir\Events\Dir\Store as DirStore;
use N1ebieski\IDir\Events\Payment\Dir\Store as PaymentStore;
use N1ebieski\IDir\Http\Responses\Web\Dir\StoreSummaryResponse;

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
            'groups' => $group->makeRepo()->getPublicWithRels()
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
        $dir->makeService()->createOrUpdateSession($request->validated());

        return redirect()->route('web.dir.create_summary', [$group->id]);
    }

    /**
     * [createSummary description]
     * @param  Group                $group    [description]
     * @param  Dir                  $dir      [description]
     * @param  Category             $category [description]
     * @param  Link                 $link     [description]
     * @param  CreateSummaryRequest $request  [description]
     * @return View                           [description]
     */
    public function createSummary(Group $group, Dir $dir, Category $category, Link $link, CreateSummaryRequest $request) : View
    {
        $dir->makeService()->createOrUpdateSession($request->validated());

        $categories = $category->makeRepo()->getByIds(
            $request->session()->get('dir.categories')
        );

        if ($group->backlink > 0) {
            $backlinks = $link->makeRepo()->getAvailableBacklinksByCats(array_merge(
                $categories->pluck('ancestors')->flatten()->pluck('id')->toArray(),
                $categories->pluck('id')->toArray()
            ));
        }

        return view('idir::web.dir.create.summary', [
            'group' => $group,
            'categories' => $categories,
            'backlinks' => $backlinks ?? null,
            'driver' => [
                'transfer' => config('idir.payment.transfer.driver'),
                'code_sms' => config('idir.payment.code_sms.driver'),
                'code_transfer' => config('idir.payment.code_transfer.driver'),
            ]
        ]);
    }

    /**
     * [storeSummary description]
     * @param  Group                $group          [description]
     * @param  Dir                  $dir            [description]
     * @param  StoreSummaryRequest  $request        [description]
     * @param  PaymentCodeRequest   $requestPayment [description]
     * @param  StoreSummaryResponse $response       [description]
     * @return RedirectResponse                     [description]
     */
    public function storeSummary(
        Group $group,
        Dir $dir,
        StoreSummaryRequest $request,
        PaymentCodeRequest $requestPayment,
        StoreSummaryResponse $response
    ) : RedirectResponse
    {
        $dir->setGroup($group)->makeService()->create($request->validated());

        if ($request->has('payment_type')) {
            event(new PaymentStore($dir->getPayment()));
        }

        event(new DirStore($dir));

        return $response->setDir($dir)->response();
    }
}
