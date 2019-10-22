<?php

namespace N1ebieski\IDir\Libs\Cashbill\Codes;

use Illuminate\Contracts\Config\Repository as Config;
use GuzzleHttp\Client;

/**
 * [Cashbill description]
 */
class SMS
{
    /**
     * [private description]
     * @var string
     */
    protected $check_url;

    /**
     * [private description]
     * @var string
     */
    protected $token;

    /**
     * [protected description]
     * @var object
     */
    private $response;

    /**
     * [__construct description]
     * @param Config $config [description]
     */
    public function __construct(Config $config)
    {
        $this->check_url = $config->get('services.cashbill.code_sms.check_url');
        $this->token = $config->get('services.cashbill.code_sms.token');
    }

    /**
     * [getResponse description]
     * @return object [description]
     */
    public function getResponse() : object
    {
        return $this->response;
    }

    /**
     * [verify description]
     * @param array $attributes [description]
     */
    public function verify(array $attributes) : void
    {
        $this->response($attributes['code']);

        if (!$this->isActive()) {
            throw new \N1ebieski\IDir\Exceptions\Cashbill\Codes\SMS\InactiveCodeException(
                'Code is inactive.', 403
            );
        }

        if (!$this->isNumber($attributes['number'])) {
            throw new \N1ebieski\IDir\Exceptions\Cashbill\Codes\SMS\InvalidNumberException(
                'Number is invalid.', 403
            );
        }
    }

    /**
     * [isActive description]
     * @return bool [description]
     */
    public function isActive() : bool
    {
        return isset($this->response->active) && (bool)$this->response->active === true;
    }

    /**
     * [isNumber description]
     * @param  int $number [description]
     * @return bool           [description]
     */
    public function isNumber(int $number) : bool
    {
        return isset($this->response->number) && (int)$this->response->number === $number;
    }

    /**
     * [response description]
     * @param string $code [description]
     */
    public function response(string $code) : void
    {
        $client = new Client();

        try {
            $response = $client->get($this->check_url . $this->token . '/' . $code);
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            throw new \N1ebieski\IDir\Exceptions\Cashbill\Exception(
                $e->getMessage(), $e->getCode()
            );
        }

        $this->response = json_decode($response->getBody());
    }
}
