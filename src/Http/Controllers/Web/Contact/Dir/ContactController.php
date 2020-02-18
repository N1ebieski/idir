<?php

namespace N1ebieski\IDir\Http\Controllers\Web\Contact\Dir;

use Illuminate\Http\JsonResponse;
use N1ebieski\IDir\Http\Requests\Web\Contact\Dir\SendRequest;
use N1ebieski\IDir\Mail\Contact\Dir\Mail as ContactMail;
use Illuminate\Support\Facades\Mail;
use N1ebieski\IDir\Http\Requests\Web\Contact\Dir\ShowRequest;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Http\Controllers\Web\Contact\Dir\Polymorphic;

/**
 * [ContactController description]
 */
class ContactController implements Polymorphic
{
    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param ShowRequest $request
     * @return JsonResponse
     */
    public function show(Dir $dir, ShowRequest $request) : JsonResponse
    {
        return response()->json([
            'view' => view('idir::web.contact.dir.show')->render()
        ]);
    }

    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param SendRequest $request
     * @param ContactMail $mail
     * @return JsonResponse
     */
    public function send(Dir $dir, SendRequest $request, ContactMail $mail) : JsonResponse
    {
        Mail::send(app()->make(ContactMail::class, ['dir' => $dir]));

        return response()->json([
            'success' => trans('idir::contact.dir.success.send')
        ]);
    }
}
