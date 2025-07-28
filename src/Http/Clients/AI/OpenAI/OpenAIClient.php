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

namespace N1ebieski\IDir\Http\Clients\AI\OpenAI;

use Illuminate\Contracts\Container\Container as App;
use N1ebieski\IDir\Http\Clients\AI\Interfaces\AIClientInterface;
use N1ebieski\IDir\Http\Clients\AI\OpenAI\Requests\ChatCompletionRequest;
use N1ebieski\IDir\Http\Clients\AI\OpenAI\Responses\ChatCompletionResponse;
use N1ebieski\IDir\Http\Clients\AI\Interfaces\Responses\ChatCompletionResponseInterface;

class OpenAIClient implements AIClientInterface
{
    public function __construct(protected App $app)
    {
    }

    public function chatCompletion(array $parameters): ChatCompletionResponseInterface
    {
        /**
         * @var ChatCompletionRequest $request
         */
        $request = $this->app->make(ChatCompletionRequest::class, [
            'parameters' => $parameters
        ]);

        /**
         * @var ChatCompletionResponse $response
         */
        $response = $this->app->make(ChatCompletionResponse::class, [
            'parameters' => json_decode($request->makeRequest()->getBody())
        ]);

        return $response;
    }
}
