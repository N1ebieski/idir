<?php

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
