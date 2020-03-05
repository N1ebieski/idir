<?php

namespace N1ebieski\IDir\Utils\Payment\Cashbill\Codes;

use Illuminate\Contracts\Config\Repository as Config;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use N1ebieski\IDir\Utils\Payment\Interfaces\Codes\TransferUtilStrategy;

/**
 * [Cashbill description]
 */
class TransferUtil implements TransferUtilStrategy
{
    /**
     * [private description]
     * @var string
     */
    protected $check_url;

    /**
     * [protected description]
     * @var object
     */
    protected object $response;

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
        $this->check_url = $config->get('services.cashbill.code_transfer.check_url');

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
        $this->makeResponse($attributes['code'], $attributes['id']);
        $this->prepareResponse();

        if (!$this->isActive()) {
            throw new \N1ebieski\IDir\Exceptions\Payment\Cashbill\Codes\Transfer\InactiveCodeException(
                'Code is inactive.',
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
        return isset($this->response->status) && (string)$this->response->status === "OK";
    }

    /**
     * Undocumented function
     *
     * @param string $code
     * @param string $id
     * @return object
     */
    public function prepareResponse() : object
    {
        $content = explode("\n", trim($this->response->getBody()->getContents()));

        return $this->response = (object)[
            'status' => $content[0],
            'timeRemaining' => $content[1] ?? null
        ];
    }

    /**
     * Undocumented function
     *
     * @param string $code
     * @param string $id
     * @return GuzzleResponse
     */
    public function makeResponse(string $code, string $id) : GuzzleResponse
    {
        try {
            $this->response = $this->guzzle->request('GET', $this->check_url . '?id=' . $id . '&check=' . $code);
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            throw new \N1ebieski\IDir\Exceptions\Payment\Cashbill\Exception(
                $e->getMessage(),
                $e->getCode()
            );
        }

        return $this->response;
    }
}
