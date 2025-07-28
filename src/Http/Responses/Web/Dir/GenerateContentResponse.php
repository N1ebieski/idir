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
use N1ebieski\IDir\Http\Clients\AI\Interfaces\Responses\ChatCompletionResponseInterface;

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

    public function makeResponse(ChatCompletionResponseInterface $response): JsonResponse
    {
        $data =  $response->getDataAsArray();

        // $data = [
        //     "content" => "OK Klima to profesjonalna firma specjalizująca się w montażu i serwisie klimatyzacji na terenie Krakowa oraz okolic. Oferta firmy skierowana jest zarówno do klientów indywidualnych, jak i biznesowych, zapewniając kompleksowe rozwiązania dopasowane do potrzeb każdego użytkownika. Specjaliści OK Klima oferują szybki, bezpieczny i bezinwazyjny montaż urządzeń klimatyzacyjnych w mieszkaniach, domach jednorodzinnych oraz lokalach komercyjnych, w tym biurach i sklepach. Firma korzysta z najwyższej jakości sprzętu renomowanych marek takich jak Toshiba, Gree, AUX oraz Mitsubishi, gwarantując długotrwałe i niezawodne działanie instalacji. Poza instalacją, OK Klima zajmuje się także serwisem, który obejmuje diagnostykę, naprawę, konserwację oraz regularne przeglądy i czyszczenie urządzeń klimatyzacyjnych i wentylacyjnych, co zapewnia ich pełną funkcjonalność i wydajność. Firma stawia na indywidualne podejście do klienta, oferując fachowe doradztwo dotyczące doboru optymalnych rozwiązań klimatyzacyjnych, a także konkurencyjne ceny i szybkość realizacji zleceń. Zapewniane przez OK Klima usługi mają na celu nie tylko poprawę komfortu termicznego, ale również zdrowia oraz efektywności w pracy, zwłaszcza w przestrzeniach biurowych, gdzie odpowiednia temperatura i wilgotność wpływają na produktywność i bezpieczeństwo urządzeń elektronicznych. Firma zlokalizowana jest w Krakowie przy ul. Królewskiej, a jej doświadczenie oraz pozytywne opinie klientów świadczą o wysokiej jakości oferowanych usług. Zapraszamy do skorzystania z oferty montażu i serwisu klimatyzacji OK Klima – gwarancja satysfakcji i komfortu na najwyższym poziomie.",
        //     "categories" => [
        //         403,
        //         1400,
        //         1118
        //     ],
        //     "tags" => "montaż klimatyzacji, serwis klimatyzacji, wentylacja Kraków, klimatyzacja domowa, klimatyzacja biurowa, klimatyzacja przemysłowa, urządzenia klimatyzacyjne, naprawa klimatyzacji, profesjonalne usługi klimatyzacyjne, firmy montujące klimatyzację"
        // ];

        if (array_key_exists('categories', $data)) {
            $data['categories'] = CategoryResource::collection(
                $this->category->whereIn('id', $data['categories'])->withAncestorsExceptSelf()->get()
            );
        }

        return $this->response->json(['data' => $data]);
    }

    public function makeDirStatusErrorResponse(): JsonResponse
    {
        return $this->response->json([
            'errors' => [
                'url' => [$this->lang->get('idir::dirs.error.generate_content.dir_status', [
                    'ip' => $this->request->ip()
                ])]
            ]
        ], HttpResponse::HTTP_NOT_FOUND);
    }

    public function makeAiErrorResponse(): JsonResponse
    {
        return $this->response->json([
            'errors' => [
                'url' => [$this->lang->get('idir::dirs.error.generate_content.ai')]
            ]
        ], HttpResponse::HTTP_NOT_FOUND);
    }
}
