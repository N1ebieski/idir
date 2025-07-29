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

namespace N1ebieski\IDir\Http\Responses\Web\Dir;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Contracts\Translation\Translator;
use N1ebieski\IDir\Models\Category\Dir\Category;
use Illuminate\Contracts\Routing\ResponseFactory;
use N1ebieski\ICore\Http\Resources\Category\CategoryResource;

class GenerateContentResponse
{
    public function __construct(
        protected ResponseFactory $response,
        protected Request $request,
        protected Category $category,
        protected Translator $lang,
    ) {
        //
    }

    public function makeResponse(array $data): JsonResponse
    {
        if (array_key_exists('categories', $data)) {
            $categories = $this->category
                ->whereIn('id', $data['categories'])
                ->active()
                ->withAncestorsExceptSelf()
                ->get();

            $data['categories'] = CategoryResource::collection($categories);
        }

        return $this->response->json(['data' => $data]);
    }

    public function makeDirStatusErrorResponse(): JsonResponse
    {
        return $this->response->json([
            'message' => $this->lang->get('idir::dirs.error.generate_content.dir_status', [
                'ip' => $this->request->server('SERVER_ADDR')
            ])
        ], HttpResponse::HTTP_BAD_GATEWAY);
    }

    public function makeAIEmptyErrorResponse(): JsonResponse
    {
        return $this->response->json([
            'message' => $this->lang->get('idir::dirs.error.generate_content.ai_empty')
        ], HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function makeAIInvalidErrorResponse(): JsonResponse
    {
        return $this->response->json([
            'message' => $this->lang->get('idir::dirs.error.generate_content.ai_invalid')
        ], HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function makeAIErrorResponse(): JsonResponse
    {
        return $this->response->json([
            'message' => $this->lang->get('idir::dirs.error.generate_content.ai')
        ], HttpResponse::HTTP_SERVICE_UNAVAILABLE);
    }
}
