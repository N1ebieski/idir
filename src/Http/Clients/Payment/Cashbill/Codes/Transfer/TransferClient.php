<?php

namespace N1ebieski\IDir\Http\Clients\Payment\Cashbill\Codes\Transfer;

use RuntimeException;
use N1ebieski\ICore\Http\Clients\Response;
use Illuminate\Contracts\Container\Container as App;
use N1ebieski\IDir\Exceptions\Payment\Cashbill\Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use N1ebieski\IDir\Http\Clients\Payment\Interfaces\Codes\TransferClientInterface;
use N1ebieski\IDir\Exceptions\Payment\Cashbill\Codes\Transfer\InactiveCodeException;
use N1ebieski\IDir\Http\Clients\Payment\Cashbill\Codes\Transfer\Requests\AuthorizeRequest;
use N1ebieski\IDir\Http\Clients\Payment\Cashbill\Codes\Transfer\Responses\AuthorizeResponse;

class TransferClient implements TransferClientInterface
{
    /**
     * Undocumented variable
     *
     * @var App
     */
    protected $app;

    /**
     * Undocumented function
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     *
     * @param array $parameters
     * @return Response
     * @throws BindingResolutionException
     * @throws Exception
     * @throws RuntimeException
     * @throws InactiveCodeException
     */
    public function authorize(array $parameters): Response
    {
        /**
         * @var AuthorizeRequest
         */
        $request = $this->app->make(AuthorizeRequest::class, [
            'parameters' => $parameters
        ]);

        $contents = explode("\n", trim($request->makeRequest()->getBody()->getContents()));

        /**
         * @var AuthorizeResponse
         */
        $response = $this->app->make(AuthorizeResponse::class, [
            'parameters' => [
                'status' => $contents[0],
                'timeRemaining' => $contents[1] ?? null
            ]
        ]);

        if (!$response->isActive()) {
            throw new \N1ebieski\IDir\Exceptions\Payment\Cashbill\Codes\Transfer\InactiveCodeException();
        }

        return $response;
    }
}
