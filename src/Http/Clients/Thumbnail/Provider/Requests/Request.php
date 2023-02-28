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

namespace N1ebieski\IDir\Http\Clients\Thumbnail\Provider\Requests;

use GuzzleHttp\ClientInterface;
use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\ICore\Http\Clients\Request as BaseRequest;

abstract class Request extends BaseRequest
{
    /**
     *
     * @var array
     */
    protected array $options = [
        'verify' => false,
    ];

    /**
     * Undocumented function
     *
     * @param array $parameters
     * @param ClientInterface $client
     * @param Config $config
     */
    public function __construct(
        array $parameters,
        ClientInterface $client,
        protected Config $config
    ) {
        parent::__construct($parameters, $client);
    }

    /**
     *
     * @return array
     */
    public function getOptions(): array
    {
        return array_merge_recursive([
            'headers' => [
                'Referer' => $this->config->get('app.url')
            ]
        ], $this->options);
    }
}
