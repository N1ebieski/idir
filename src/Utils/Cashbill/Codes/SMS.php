<?php

namespace N1ebieski\IDir\Utils\Cashbill\Codes;

use Illuminate\Contracts\Config\Repository as Config;
use GuzzleHttp\Client as GuzzleClient;

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
    protected $response;

    /**
     * [protected description]
     * @var GuzzleClient
     */
    protected $guzzle;

    /**
     * [__construct description]
     * @param Config       $config [description]
     * @param GuzzleClient $guzzle [description]
     */
    public function __construct(Config $config, GuzzleClient $guzzle)
    {
        $this->check_url = $config->get('services.cashbill.code_sms.check_url');
        $this->token = $config->get('services.cashbill.code_sms.token');

        $this->guzzle = $guzzle;    
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
        try {
            $response = $this->guzzle->request('GET', $this->check_url . $this->token . '/' . $code);
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            throw new \N1ebieski\IDir\Exceptions\Cashbill\Exception(
                $e->getMessage(), $e->getCode()
            );
        }

        $this->response = json_decode($response->getBody());
    }
}
