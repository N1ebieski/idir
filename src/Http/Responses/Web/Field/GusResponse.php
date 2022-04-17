<?php

namespace N1ebieski\IDir\Http\Responses\Web\Field;

use Illuminate\Http\JsonResponse;
use GusApi\SearchReport as GusReport;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Contracts\Translation\Translator;
use N1ebieski\IDir\Http\Responses\Data\Gus\Field\FieldData;
use Illuminate\Contracts\Routing\ResponseFactory as Response;
use N1ebieski\IDir\Http\Responses\Web\Field\JsonResponseFactory;

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
     * @param FieldData $fieldData
     */
    public function __construct(
        Response $response,
        Translator $lang,
        FieldData $fieldData
    ) {
        $this->response = $response;
        $this->lang = $lang;

        $this->fieldData = $fieldData;
    }

    /**
     * Undocumented function
     *
     * @param GusReport $gusReport
     * @return JsonResponse
     */
    public function makeResponse(GusReport $gusReport = null): JsonResponse
    {
        if ($gusReport === null) {
            return $this->response->json([
                'errors' => [
                    'gus' => [$this->lang->get('idir::fields.error.gus')]
                ]
            ], HttpResponse::HTTP_NOT_FOUND);
        }

        return $this->response->json([
            'data' => $this->fieldData->toArray($gusReport)
        ]);
    }
}
