<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\IDir\Http\Responses\Admin\Field;

use Illuminate\Http\JsonResponse;
use GusApi\SearchReport as GusReport;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Contracts\Translation\Translator;
use N1ebieski\IDir\Http\Responses\Data\Gus\Field\FieldData;
use Illuminate\Contracts\Routing\ResponseFactory as Response;
use N1ebieski\IDir\Http\Responses\Admin\Field\JsonResponseFactory;

class GusResponse implements JsonResponseFactory
{
    /**
     * Undocumented function
     *
     * @param Response $response
     * @param Translator $lang
     * @param FieldData $fieldData
     */
    public function __construct(
        protected Response $response,
        protected Translator $lang,
        protected FieldData $fieldData
    ) {
        //
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
