<?php

namespace N1ebieski\IDir\Utils\Cashbill\Codes;

use Illuminate\Contracts\Config\Repository as Config;
use GuzzleHttp\Client as GuzzleClient;

/**
 * [Cashbill description]
 */
class Transfer
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
        $this->check_url = $config->get('services.cashbill.code_transfer.check_url');

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
     * [authorize description]
     * @param array $attributes [description]
     */
    public function authorize(array $attributes) : void
    {
        $this->response($attributes['code'], $attributes['id']);

        if (!$this->isActive()) {
            throw new \N1ebieski\IDir\Exceptions\Cashbill\Codes\Transfer\InactiveCodeException(
                'Code is inactive.', 403
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
     * [response description]
     * @param string $code [description]
     * @param string $id   [description]
     */
    public function response(string $code, string $id) : void
    {
        try {
            $response = $this->guzzle->request('GET', $this->check_url . '?id=' . $id . '&check=' . $code);
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            throw new \N1ebieski\IDir\Exceptions\Cashbill\Exception(
                $e->getMessage(), $e->getCode()
            );
        }

        $this->prepareResponse($response->getBody()->getContents());
    }

    /**
     * [prepareResponse description]
     * @param string $body [description]
     */
    protected function prepareResponse(string $body) : void
    {
        $body = explode("\n", trim($body));

        $this->response = (object)[
            'status' => $body[0],
            'timeRemaining' => $body[1] ?? null
        ];
    }
}
