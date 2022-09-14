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

namespace N1ebieski\IDir\Http\Clients\Payment\Cashbill\Codes\SMS;

use N1ebieski\ICore\Http\Clients\Response;
use Illuminate\Contracts\Container\Container as App;
use N1ebieski\IDir\Http\Clients\Payment\Interfaces\Codes\SmsClientInterface;
use N1ebieski\IDir\Http\Clients\Payment\Cashbill\Codes\SMS\Requests\AuthorizeRequest;
use N1ebieski\IDir\Http\Clients\Payment\Cashbill\Codes\SMS\Responses\AuthorizeResponse;

class SmsClient implements SmsClientInterface
{
    /**
     * Undocumented function
     *
     * @param App $app
     */
    public function __construct(protected App $app)
    {
        //
    }

    /**
     * Undocumented function
     *
     * @param array $parameters
     * @return Response
     */
    public function authorize(array $parameters): Response
    {
        /**
         * @var AuthorizeRequest
         */
        $request = $this->app->make(AuthorizeRequest::class, [
            'parameters' => $parameters
        ]);

        /**
         * @var AuthorizeResponse
         */
        $response = $this->app->make(AuthorizeResponse::class, [
            'parameters' => json_decode($request->makeRequest()->getBody())
        ]);

        if (!$response->isActive()) {
            throw new \N1ebieski\IDir\Exceptions\Payment\Cashbill\Codes\SMS\InactiveCodeException();
        }

        if (!$response->isNumber($request->get('number'))) {
            throw new \N1ebieski\IDir\Exceptions\Payment\Cashbill\Codes\SMS\InvalidNumberException();
        }

        return $response;
    }
}
