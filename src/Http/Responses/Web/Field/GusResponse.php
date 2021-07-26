<?php

namespace N1ebieski\IDir\Http\Responses\Web\Field;

use Illuminate\Http\JsonResponse;
use GusApi\SearchReport as GusReport;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\ICore\Http\Responses\JsonResponseFactory;
use N1ebieski\IDir\Http\Responses\Data\Field\FieldData;
use Illuminate\Contracts\Routing\ResponseFactory as Response;

class GusResponse implements JsonResponseFactory
{
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
     * [protected description]
     * @var Config
     */
    protected $config;

    /**
     * Undocumented variable
     *
     * @var FieldData
     */
    protected $fieldData;

    /**
     * Undocumented function
     *
     * @param Response $response
     * @param Translator $lang
     * @param Config $config
     * @param FieldData $fieldData
     */
    public function __construct(
        Response $response,
        Translator $lang,
        Config $config,
        FieldData $fieldData
    ) {
        $this->response = $response;
        $this->lang = $lang;
        $this->config = $config;
        
        $this->fieldData = $fieldData;
    }

    /**
     * Undocumented function
     *
     * @param GusReport $gusReport
     * @return JsonResponse
     */
    public function makeResponse(GusReport $gusReport = null) : JsonResponse
    {
        if ($gusReport === null) {
            return $this->response->json([
                'errors' => [
                    'gus' => [$this->lang->get('idir::fields.error.gus')]
                ]
            ], 404);
        }

        return $this->response->json([
            'success' => '',
            'data' => $this->makeFieldDataToArray($gusReport)
        ]);
    }

    /**
     * Undocumented function
     *
     * @param GusReport $gusReport
     * @return array
     */
    protected function makeFieldDataToArray(GusReport $gusReport) : array
    {
        return $this->fieldData->setGusReport($gusReport)->toArray();
    }
}
