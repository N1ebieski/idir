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

namespace N1ebieski\IDir\Http\Clients\AI\OpenAI\Responses;

use N1ebieski\ICore\Http\Clients\Response;
use N1ebieski\IDir\Http\Clients\AI\Interfaces\Responses\ChatCompletionResponseInterface;

class ChatCompletionResponse extends Response implements ChatCompletionResponseInterface
{
    public function getData(): string
    {
        if (empty($this->parameters->choices)) {
            throw new \N1ebieski\IDir\Exceptions\AI\EmptyChoiceException();
        }

        if (empty($this->parameters->choices[0]->message->content)) {
            throw new \N1ebieski\IDir\Exceptions\AI\EmptyMessageException();
        }

        return $this->parameters->choices[0]->message->content;
    }

    public function getDataAsArray(): array
    {
        $json = json_decode($this->getData(), true);

        if ($json === null) {
            throw new \N1ebieski\IDir\Exceptions\AI\InvalidJsonException();
        }

        return $json;
    }
}
