<?php

namespace N1ebieski\IDir\Http\Clients;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;

abstract class Client
{
    /**
     * Undocumented variable
     *
     * @var ClientInterface
     */
    protected $client;

    /**
     * Undocumented variable
     *
     * @var ResponseInterface
     */
    protected $response;

    /**
     * Undocumented variable
     *
     * @var object|string
     */
    protected $contents;

    /**
     * Undocumented function
     *
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Undocumented function
     *
     * @param array $query
     * @return static
     */
    public function setQuery(array $query)
    {
        foreach ($query as $key => $value) {
            if (is_int($key)) {
                $this->uri = preg_replace('/({[a-z0-9]+})/', $value, $this->uri, 1);

                unset($query[$key]);
            } else {
                if (strpos($this->uri, '{' . $key . '}') === false) {
                    continue;
                }

                $this->uri = str_replace('{' . $key . '}', $value, $this->uri);

                unset($query[$key]);
            }
        }

        if (!empty($query)) {
            $this->uri .= '?' . http_build_query($query);
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array $params
     * @return static
     */
    public function setParams(array $params)
    {
        if (!isset($this->options['form_params'])) {
            $this->options['form_params'] = [];
        }

        $this->options['form_params'] = array_replace_recursive(
            $this->options['form_params'],
            $params
        );

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array $headers
     * @return static
     */
    public function setHeaders(array $headers)
    {
        if (!isset($this->options['headers'])) {
            $this->options['headers'] = [];
        }

        $this->options['headers'] = array_replace_recursive(
            $this->options['headers'],
            $headers
        );

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param ResponseInterface $response
     * @return static
     */
    protected function setResponse(ResponseInterface $response)
    {
        $this->response = $response;

        $this->setContentsFromResponse($response);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param ResponseInterface $response
     * @return static
     */
    protected function setContentsFromResponse(ResponseInterface $response)
    {
        $this->contents = json_decode($response->getBody());

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @return static
     */
    protected function setUrl(string $url)
    {
        $this->setHostFromUrl($url);
        $this->setUriFromUrl($url);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @return static
     */
    protected function setHostFromUrl(string $url)
    {
        $result = parse_url($url);

        $this->host = $result['scheme'] . '://' . $result['host'];
        $this->host .= isset($result['port']) ? ':' . $result['port'] : '';

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @return static
     */
    protected function setUriFromUrl(string $url)
    {
        $result = parse_url($url);

        $this->uri = $result['path'];
        $this->uri .= isset($result['query']) ? '?' . $result['query'] : '';
        $this->uri .= isset($result['fragment']) ? '#' . $result['fragment'] : '';

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * Undocumented function
     *
     * @return object|string
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * Undocumented function
     *
     * @param array|null $query
     * @param array|null $params
     * @return void
     */
    public function request(array $query = null, array $params = null)
    {
        if (!empty($query)) {
            $this->setQuery($query);
        }

        if (!empty($params)) {
            $this->setParams($params);
        }

        $this->setResponse(
            $this->makeResponse()
        );

        return $this->getContents();
    }

    /**
     * Undocumented function
     *
     * @return ResponseInterface
     */
    protected function makeResponse(): ResponseInterface
    {
        return $this->client->request($this->method, $this->host . $this->uri, $this->options);
    }
}
