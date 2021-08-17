<?php

namespace N1ebieski\IDir\Utils\Payment\Cashbill\Codes;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\IDir\Utils\Payment\Interfaces\Codes\TransferUtilStrategy;

class TransferUtil implements TransferUtilStrategy
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
        $this->check_url = $config->get('services.cashbill.code_transfer.check_url');

        $this->guzzle = $guzzle;
    }

    /**
     * Undocumented function
     *
     * @param string $code
     * @param string $id
     * @return static
     */
    protected function setContentsFromResponse(GuzzleResponse $response)
    {
        $contents = explode("\n", trim($response->getBody()->getContents()));

        $this->contents = (object)[
            'status' => $contents[0],
            'timeRemaining' => $contents[1] ?? null
        ];

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
        return isset($this->contents->status) && (string)$this->contents->status === "OK";
    }

    /**
     * [authorize description]
     * @param array $attributes [description]
     */
    public function authorize(array $attributes): void
    {
        $this->setContentsFromResponse(
            $this->makeResponse($attributes['code'], $attributes['id'])
        );

        if (!$this->isActive()) {
            throw new \N1ebieski\IDir\Exceptions\Payment\Cashbill\Codes\Transfer\InactiveCodeException(
                'Code is inactive.',
                403
            );
        }
    }

    /**
     * Undocumented function
     *
     * @param string $code
     * @param string $id
     * @return GuzzleResponse
     */
    public function makeResponse(string $code, string $id): GuzzleResponse
    {
        try {
            $response = $this->guzzle->request('GET', $this->check_url . '?id=' . $id . '&check=' . $code);
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            throw new \N1ebieski\IDir\Exceptions\Payment\Cashbill\Exception(
                $e->getMessage(),
                $e->getCode()
            );
        }

        return $response;
    }
}
