<?php

namespace N1ebieski\IDir\Http\Clients\Thumbnail;

use GuzzleHttp\ClientInterface;
use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\ICore\Http\Clients\Client as BaseClient;

class Client extends BaseClient
{
    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $options = [
        'verify' => false
    ];

    /**
     * Temporary fix for users who use the old pattern url
     *
     * @param string $url
     * @return static
     */
    protected function setUrl(string $url)
    {
        $this->url = $url;

        if (strpos($url, '{url}') === false) {
            $this->url .= '{url}';
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param ClientInterface $client
     * @param Config $config
     */
    public function __construct(ClientInterface $client, Config $config)
    {
        parent::__construct($client);

        $this->setHeaders(['Authorization' => $config->get('idir.dir.thumbnail.key')]);
    }
}
