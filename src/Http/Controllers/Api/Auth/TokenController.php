<?php

namespace N1ebieski\IDir\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use N1ebieski\ICore\Http\Controllers\Api\Auth\TokenController as BaseTokenController;

/**
 * @group Authentication
 *
 * > Routes:
 *
 *     /routes/vendor/icore/api/auth.php
 *
 * > Controllers:
 *
 *     N1ebieski\ICore\Http\Controllers\Api\Auth\RegisterController
 *     N1ebieski\ICore\Http\Controllers\Api\Auth\TokenController
 *
 */
class TokenController
{
    /**
     * Undocumented variable
     *
     * @var BaseTokenController
     */
    protected $decorated;

    /**
     * Undocumented function
     *
     * @param BaseTokenController $decorated
     */
    public function __construct(BaseTokenController $decorated)
    {
        $this->decorated = $decorated;
    }

    /**
     * Token
     *
     * Create access token and (optional) refresh token.
     *
     * <aside class="notice">Access token expires after 2 hours. Refresh token expires after 1 year.</aside>
     *
     * @unauthenticated
     *
     * @bodyParam email string Example: kontakt@demo.idir.intelekt.net.pl
     * @bodyParam password string Example: demo1234
     * @bodyParam remember boolean Example: true
     *
     * @response 201 scenario=success {
     *  "access_token": "1|LN8Gmqe6cRHQpPr5X5GW9wFXuqHVK09L8FSb7Ju9",
     *  "refresh_token": "2|hVHAhrgiBmSbyYjbVAC17wjwAJyKK8TQmhglBAtM"
     * }
     *
     * @responseField access_token string
     * @responseField refresh_token string (only if remember param was true)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function token(Request $request): JsonResponse
    {
        return $this->decorated->token($request);
    }
}