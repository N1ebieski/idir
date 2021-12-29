<?php

namespace N1ebieski\IDir\Http\Clients\Thumbnail\Provider;

use GuzzleHttp\ClientInterface;
use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\IDir\Http\Clients\Thumbnail\Provider\Client;

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
    protected $uri = '/v2/thumbs.php?size=x&url={url}';

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
        parent::__construct($client);

        $url = $config->get('idir.dir.thumbnail.url');

        if (!empty($url)) {
            $this->setUrl($this->url($url));
        }
    }
}
