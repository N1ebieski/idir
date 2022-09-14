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

namespace N1ebieski\IDir\Http\Clients\Payment\Cashbill\Codes\SMS\Requests;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use N1ebieski\ICore\Http\Clients\Intelekt\Request;
use Illuminate\Contracts\Config\Repository as Config;

class AuthorizeRequest extends Request
{
    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $method = 'GET';

    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $options = [
        'verify' => false
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
     * Undocumented function
     *
     * @return ResponseInterface
     * @throws \N1ebieski\IDir\Exceptions\Payment\Cashbill\Exception
     */
    public function makeRequest(): ResponseInterface
    {
        try {
            $response = $this->client->request(
                $this->method,
                $this->getCashbillUrl(),
                $this->options
            );
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            throw new \N1ebieski\IDir\Exceptions\Payment\Cashbill\Exception(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }

        return $response;
    }

    /**
     * Temporary fix for users who use the old pattern url
     *
     * @return string
     */
    protected function getCashbillUrl(): string
    {
        $cashbillUrl = $this->config->get('services.cashbill.code_sms.check_url');

        if (strpos($cashbillUrl, '{token}') === false) {
            $cashbillUrl .= '{token}';
        }

        if (strpos($cashbillUrl, '{code}') === false) {
            $cashbillUrl .= '/{code}';
        }

        return str_replace(['{token}', '{code}'], [$this->get('token'), $this->get('code')], $cashbillUrl);
    }
}
