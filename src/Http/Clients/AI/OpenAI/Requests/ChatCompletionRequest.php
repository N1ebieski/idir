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

namespace N1ebieski\IDir\Http\Clients\AI\OpenAI\Requests;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use N1ebieski\ICore\Http\Clients\Request;
use Illuminate\Contracts\Config\Repository as Config;

class ChatCompletionRequest extends Request
{
    protected string $method = 'POST';

    protected string $uri = 'https://api.openai.com/v1/chat/completions';

    protected array $options = [
        'timeout' => 60,
        'headers' => [
            'Accept' => 'application/json'
        ],
        'verify' => false
    ];

    public function __construct(
        array $parameters,
        ClientInterface $client,
        protected Config $config
    ) {
        parent::__construct($parameters, $client);
    }

    public function makeRequest(): ResponseInterface
    {
        try {
            $response = $this->client->request(
                $this->method,
                $this->uri,
                array_merge_recursive($this->options, [
                    'headers' => [
                        'Authorization' => "Bearer {$this->config->get('services.openai.api_key')}",
                    ],
                    'json' => $this->parameters
                ])
            );
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            throw new \N1ebieski\IDir\Exceptions\AI\OpenAI\Exception(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }

        return $response;
    }
}
