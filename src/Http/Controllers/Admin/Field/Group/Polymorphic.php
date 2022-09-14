<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\IDir\Http\Controllers\Admin\Field\Group;

use N1ebieski\IDir\Models\Group;
use Illuminate\Http\JsonResponse;
use N1ebieski\IDir\Models\Field\Group\Field;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Filters\Admin\Field\Group\IndexFilter;
use N1ebieski\IDir\Http\Requests\Admin\Field\Group\IndexRequest;
use N1ebieski\IDir\Http\Requests\Admin\Field\Group\StoreRequest;
use N1ebieski\IDir\Http\Requests\Admin\Field\Group\UpdateRequest;

interface Polymorphic
{
    /**
     * [index description]
     * @param  Field        $field   [description]
     * @param  Group        $group   [description]
     * @param  IndexRequest $request [description]
     * @param  IndexFilter  $filter  [description]
     * @return HttpResponse          [description]
     */
    public function index(Field $field, Group $group, IndexRequest $request, IndexFilter $filter): HttpResponse;

    /**
     * [create description]
     * @param  Field        $field [description]
     * @param  Group        $group [description]
     * @return JsonResponse        [description]
     */
    public function create(Field $field, Group $group): JsonResponse;

    /**
     * [store description]
     * @param  Field        $field   [description]
     * @param  StoreRequest $request [description]
     * @return JsonResponse          [description]
     */
    public function store(Field $field, StoreRequest $request): JsonResponse;

    /**
     * [edit description]
     * @param  Field        $field [description]
     * @param  Group        $group [description]
     * @return JsonResponse        [description]
     */
    public function edit(Field $field, Group $group): JsonResponse;

    /**
     * [update description]
     * @param  Field         $field   [description]
     * @param  UpdateRequest $request [description]
     * @return JsonResponse           [description]
     */
    public function update(Field $field, UpdateRequest $request): JsonResponse;
}
