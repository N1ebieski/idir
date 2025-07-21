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
use N1ebieski\IDir\Http\Clients\AI\OpenAI\Requests\ChatCompletionRequest;

class OpenAIClient
{
    public function __construct(protected App $app)
    {
    }

    public function chatCompletion(array $parameters)
    {
        /**
         * @var ChatCompletionRequest $request
         */
        $request = $this->app->make(ChatCompletionRequest::class, [
            'parameters' => $parameters
        ]);

        return $request->makeRequest();
    }
}
