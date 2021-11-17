<?php

namespace N1ebieski\IDir\Http\Middleware\Api\Thumbnail;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class VerifyKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->header('Authorization') === Config::get('idir.dir.thumbnail.key')) {
            return $next($request);
        }

        App::abort(Response::HTTP_UNAUTHORIZED);
    }
}
