<?php

namespace N1ebieski\IDir\Http\Controllers\Web\Contact\Dir;

use Illuminate\Http\JsonResponse;
use N1ebieski\IDir\Http\Requests\Web\Contact\Dir\SendRequest;
use N1ebieski\IDir\Mail\Contact\Dir\Mail as ContactMail;
use N1ebieski\IDir\Http\Requests\Web\Contact\Dir\ShowRequest;
use N1ebieski\IDir\Models\Dir;

interface Polymorphic
{
    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param ShowRequest $request
     * @return JsonResponse
     */
    public function show(Dir $dir, ShowRequest $request) : JsonResponse;


    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param SendRequest $request
     * @return JsonResponse
     */
    public function send(Dir $dir, SendRequest $request) : JsonResponse;
}
