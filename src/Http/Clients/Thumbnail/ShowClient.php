<?php

namespace N1ebieski\IDir\Http\Clients\Thumbnail;

use GuzzleHttp\ClientInterface;
use N1ebieski\IDir\Http\Clients\Thumbnail\Client;
use Illuminate\Contracts\Config\Repository as Config;

class ShowClient extends Client
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
     * @var string
     */
    protected $uri;

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $host;

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
     * @param ClientInterface $client
     * @param Config $config
     */
    public function __construct(ClientInterface $client, Config $config)
    {
        $this->client = $client;

        $showUrl = $config->get('idir.dir.thumbnail.url') . '{url}';

        if (!empty($showUrl)) {
            $this->setUrl($showUrl);
        }
    }
}
