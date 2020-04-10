<?php

namespace N1ebieski\IDir\Utils\Payment\Cashbill\Codes;

use Illuminate\Contracts\Config\Repository as Config;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use N1ebieski\IDir\Utils\Payment\Interfaces\Codes\SMSUtilStrategy;

/**
 * [Cashbill description]
 */
class SMSUtil implements SMSUtilStrategy
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
     * Get [protected description]
     *
     * @return  object
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * [authorize description]
     * @param array $attributes [description]
     */
    public function authorize(array $attributes) : void
    {
        $this->makeResponse($attributes['code']);
        $this->prepareResponse();

        if (!$this->isActive()) {
            throw new \N1ebieski\IDir\Exceptions\Payment\Cashbill\Codes\SMS\InactiveCodeException(
                'Code is inactive.',
                403
            );
        }

        if (!$this->isNumber($attributes['number'])) {
            throw new \N1ebieski\IDir\Exceptions\Payment\Cashbill\Codes\SMS\InvalidNumberException(
                'Number is invalid.',
                403
            );
        }
    }

    /**
     * [isActive description]
     * @return bool [description]
     */
    public function isActive() : bool
    {
        return isset($this->response->active)
            && (bool)$this->response->active === true;
    }

    /**
     * [isNumber description]
     * @param  int $number [description]
     * @return bool           [description]
     */
    public function isNumber(int $number) : bool
    {
        return isset($this->response->number)
            && (int)$this->response->number === $number;
    }

    /**
     * Undocumented function
     *
     * @return object
     */
    public function prepareResponse() : object
    {
        return $this->response = json_decode($this->response->getBody());
    }

    /**
     * Undocumented function
     *
     * @param string $code
     * @return GuzzleResponse
     */
    public function makeResponse(string $code) : GuzzleResponse
    {
        try {
            $this->response = $this->guzzle->request('GET', $this->check_url . $this->token . '/' . $code);
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            throw new \N1ebieski\IDir\Exceptions\Payment\Cashbill\Exception(
                $e->getMessage(),
                $e->getCode()
            );
        }

        return $this->response;
    }
}
