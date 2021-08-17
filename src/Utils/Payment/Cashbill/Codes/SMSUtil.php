<?php

namespace N1ebieski\IDir\Utils\Payment\Cashbill\Codes;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\IDir\Utils\Payment\Interfaces\Codes\SMSUtilStrategy;

class SMSUtil implements SMSUtilStrategy
{
    /**
     * [protected description]
     * @var GuzzleClient
     */
    protected $guzzle;

    /**
     * [private description]
     * @var string
     */
    protected $check_url;

    /**
     * [protected description]
     * @var object
     */
    protected $contents;

    /**
     * [__construct description]
     * @param Config       $config [description]
     * @param GuzzleClient $guzzle [description]
     */
    public function __construct(Config $config, GuzzleClient $guzzle)
    {
        $this->check_url = $config->get('services.cashbill.code_sms.check_url');

        $this->guzzle = $guzzle;
    }

    /**
     * Undocumented function
     *
     * @param GuzzleResponse $response
     * @return static
     */
    protected function setContentsFromResponse(GuzzleResponse $response)
    {
        $this->contents = json_decode($response->getBody());

        return $this;
    }

    /**
     * Get [protected description]
     *
     * @return  object
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * [isActive description]
     * @return bool [description]
     */
    public function isActive(): bool
    {
        return isset($this->contents->active)
            && (bool)$this->contents->active === true;
    }

    /**
     * [isNumber description]
     * @param  int $number [description]
     * @return bool           [description]
     */
    public function isNumber(int $number): bool
    {
        return isset($this->contents->number)
            && (int)$this->contents->number === $number;
    }

    /**
     * [authorize description]
     * @param array $attributes [description]
     */
    public function authorize(array $attributes): void
    {
        $this->setContentsFromResponse(
            $this->makeResponse($attributes['token'], $attributes['code'])
        );

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
     * Undocumented function
     *
     * @param string $token
     * @param string $code
     * @return GuzzleResponse
     */
    public function makeResponse(string $token, string $code): GuzzleResponse
    {
        try {
            $response = $this->guzzle->request('GET', $this->check_url . $token . '/' . $code);
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            throw new \N1ebieski\IDir\Exceptions\Payment\Cashbill\Exception(
                $e->getMessage(),
                $e->getCode()
            );
        }

        return $response;
    }
}
