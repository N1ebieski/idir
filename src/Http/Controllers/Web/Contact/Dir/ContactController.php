<?php

namespace N1ebieski\IDir\Http\Controllers\Web\Contact\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Mail\Contact\Dir\Mail as ContactMail;
use Illuminate\Contracts\Debug\ExceptionHandler as Exception;
use N1ebieski\IDir\Http\Requests\Web\Contact\Dir\SendRequest;
use N1ebieski\IDir\Http\Requests\Web\Contact\Dir\ShowRequest;
use N1ebieski\IDir\Http\Controllers\Web\Contact\Dir\Polymorphic;

class ContactController implements Polymorphic
{
    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param ShowRequest $request
     * @return JsonResponse
     */
    public function show(Dir $dir, ShowRequest $request): JsonResponse
    {
        return Response::json([
            'view' => View::make('idir::web.contact.dir.show')->render()
        ]);
    }

    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param SendRequest $request
     * @param Exception $exception
     * @return JsonResponse
     */
    public function send(Dir $dir, SendRequest $request, Exception $exception): JsonResponse
    {
        try {
            Mail::send(App::make(ContactMail::class, ['dir' => $dir]));
        } catch (\Throwable $e) {
            $exception->report($e);

            App::abort(
                HttpResponse::HTTP_FORBIDDEN,
                'An error occurred when trying to send a message.'
            );
        }

        return Response::json([
            'success' => Lang::get('idir::contact.dir.success.send')
        ]);
    }
}
