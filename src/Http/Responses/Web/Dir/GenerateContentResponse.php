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

use Mews\Purifier\Purifier;
use Illuminate\Http\Request;
use N1ebieski\IDir\Models\Group;
use Illuminate\Http\JsonResponse;
use N1ebieski\ICore\Exceptions\AI\Exception;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Translation\Translator;
use N1ebieski\IDir\Models\Category\Dir\Category;
use Illuminate\Contracts\Routing\ResponseFactory;
use N1ebieski\IDir\Exceptions\DirStatus\TransferException;
use N1ebieski\ICore\Http\Resources\Category\CategoryResource;
use N1ebieski\ICore\Http\Clients\AI\Interfaces\Responses\ChatCompletionResponseInterface;

class GenerateContentResponse
{
    public function __construct(
        protected ResponseFactory $response,
        protected Request $request,
        protected Category $category,
        protected Translator $lang,
        protected ExceptionHandler $exceptionHandler,
        protected Purifier $purifier
    ) {
        //
    }

    public function makeResponse(ChatCompletionResponseInterface $response, Group $group): JsonResponse
    {
        try {
            $data = $response->getDataAsArray();
        } catch (\N1ebieski\ICore\Exceptions\AI\Exception $e) {
            return $this->makeErrorResponse($e);
        }

        if (array_key_exists('categories', $data)) {
            $categories = $this->category
                ->whereIn('id', $data['categories'])
                ->active()
                ->withAncestorsExceptSelf()
                ->get();

            $data['categories'] = CategoryResource::collection($categories);
        }

        if (array_key_exists('content', $data)) {
            $data['content'] = $group->hasEditorPrivilege()
                ? $this->purifier->clean($data['content'], 'dir')
                : strip_tags($data['content']);
        }

        return $this->response->json(['data' => $data]);
    }

    public function makeErrorResponse(Exception|TransferException $exception): JsonResponse
    {
        $this->exceptionHandler->report($exception);

        return $this->response->json([
            'message' => match (get_class($exception)) { //@phpstan-ignore-line
                \N1ebieski\IDir\Exceptions\DirStatus\TransferException::class => $this->lang->get('idir::dirs.error.generate_content.dir_status', [
                    'ip' => $this->request->server('SERVER_ADDR')
                ]),
                \N1ebieski\ICore\Exceptions\AI\EmptyChoiceException::class,
                \N1ebieski\ICore\Exceptions\AI\EmptyMessageException::class => $this->lang->get('idir::dirs.error.generate_content.ai_empty'),
                \N1ebieski\ICore\Exceptions\AI\InvalidJsonException::class => $this->lang->get('idir::dirs.error.generate_content.ai_invalid'),
                \N1ebieski\ICore\Exceptions\AI\Exception::class => $this->lang->get('idir::dirs.error.generate_content.ai')
            }
        ], match (get_class($exception)) { //@phpstan-ignore-line
                \N1ebieski\IDir\Exceptions\DirStatus\TransferException::class => HttpResponse::HTTP_BAD_GATEWAY,
                \N1ebieski\ICore\Exceptions\AI\EmptyChoiceException::class,
                \N1ebieski\ICore\Exceptions\AI\EmptyMessageException::class => HttpResponse::HTTP_UNPROCESSABLE_ENTITY,
                \N1ebieski\ICore\Exceptions\AI\InvalidJsonException::class => HttpResponse::HTTP_UNPROCESSABLE_ENTITY,
                \N1ebieski\ICore\Exceptions\AI\Exception::class => HttpResponse::HTTP_SERVICE_UNAVAILABLE
        });
    }
}
