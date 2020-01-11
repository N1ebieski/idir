<?php

namespace N1ebieski\IDir\Http\Middleware\Api\Thumbnail;

use Closure;
use Illuminate\Http\Response;

class VerifyKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->header('Authorization') === config('idir.dir.thumbnail.key')) {
            return $next($request);
        }

        abort(Response::HTTP_UNAUTHORIZED);
    }
}