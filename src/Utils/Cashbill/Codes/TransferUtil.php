<?php

namespace N1ebieski\IDir\Utils\Cashbill\Codes;

use Illuminate\Contracts\Config\Repository as Config;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response as GuzzleResponse;

/**
 * [Cashbill description]
 */
class TransferUtil
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
    public $response;

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
     * [authorize description]
     * @param array $attributes [description]
     */
    public function authorize(array $attributes) : void
    {
        $this->prepareResponse($attributes['code'], $attributes['id']);

        if (!$this->isActive()) {
            throw new \N1ebieski\IDir\Exceptions\Cashbill\Codes\Transfer\InactiveCodeException(
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
    public function prepareResponse(string $code, string $id) : object
    {
        $this->makeResponse($code, $id);

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
            throw new \N1ebieski\IDir\Exceptions\Cashbill\Exception(
                $e->getMessage(),
                $e->getCode()
            );
        }

        return $this->response;
    }
}
