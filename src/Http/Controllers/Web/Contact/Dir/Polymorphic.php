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

namespace N1ebieski\IDir\Http\Controllers\Web\Contact\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Debug\ExceptionHandler as Exception;
use N1ebieski\IDir\Http\Requests\Web\Contact\Dir\SendRequest;
use N1ebieski\IDir\Http\Requests\Web\Contact\Dir\ShowRequest;

interface Polymorphic
{
    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param ShowRequest $request
     * @return JsonResponse
     */
    public function show(Dir $dir, ShowRequest $request): JsonResponse;

    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param SendRequest $request
     * @param Exception $exception
     * @return JsonResponse
     */
    public function send(Dir $dir, SendRequest $request, Exception $exception): JsonResponse;
}
