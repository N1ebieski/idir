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

namespace N1ebieski\IDir\Http\Controllers\Web\Comment\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Http\JsonResponse;
use N1ebieski\IDir\Models\Comment\Dir\Comment;
use N1ebieski\IDir\Http\Requests\Web\Comment\Dir\StoreRequest;
use N1ebieski\IDir\Http\Requests\Web\Comment\Dir\CreateRequest;

interface Polymorphic
{
    /**
     * Show the form for creating a new Comment for Dir.
     *
     * @param  Dir          $dir    [description]
     * @param  CreateRequest $request [description]
     * @return JsonResponse           [description]
     */
    public function create(Dir $dir, CreateRequest $request): JsonResponse;

    /**
     * [store description]
     * @param  Dir         $dir    [description]
     * @param  Comment      $comment [description]
     * @param  StoreRequest $request [description]
     * @return JsonResponse          [description]
     */
    public function store(Dir $dir, Comment $comment, StoreRequest $request): JsonResponse;
}
