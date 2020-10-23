<?php

namespace N1ebieski\IDir\Http\Responses\Field;

use Illuminate\Http\JsonResponse;
use GusApi\SearchReport as GusReport;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Container\Container as App;
use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\ICore\Http\Responses\JsonResponseFactory;
use Illuminate\Contracts\Routing\ResponseFactory as Response;
use N1ebieski\IDir\Http\Responses\Field\Data\FieldData;

class GusResponse implements JsonResponseFactory
{
    /**
     * [private description]
     * @var GusReport
     */
    protected $gusReport;

    /**
     * [private description]
     * @var Response
     */
    protected $response;

    /**
     * [protected description]
     * @var Translator
     */
    protected $lang;

    /**
     * Undocumented variable
     *
     * @var App
     */
    protected $app;

    /**
     * Undocumented variable
     *
     * @var array|null
     */
    protected $fields;

    /**
     * Undocumented function
     *
     * @param Response $response
     * @param Translator $lang
     * @param Config $config
     * @param App $app
     * @param GusReport $gusReport
     */
    public function __construct(
        Response $response,
        Translator $lang,
        Config $config,
        App $app,
        GusReport $gusReport = null
    ) {
        $this->response = $response;
        $this->lang = $lang;
        $this->config = $config;
        $this->app = $app;

        $this->gusReport = $gusReport;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function isGusReport() : bool
    {
        return $this->gusReport !== null;
    }

    /**
     * [response description]
     * @return JsonResponse [description]
     */
    public function makeResponse() : JsonResponse
    {
        if (!$this->isGusReport()) {
            return $this->response->json([
                'errors' => [
                    'gus' => [$this->lang->get('idir::fields.error.gus')]
                ]
            ], 404);
        }

        return $this->response->json([
            'success' => '',
            'data' => $this->makeFieldData()->toArray()
        ]);
    }

    /**
     * Undocumented function
     *
     * @return FieldData
     */
    protected function makeFieldData() : FieldData
    {
        return $this->app->make(FieldData::class, ['gusReport' => $this->gusReport]);
    }
}
