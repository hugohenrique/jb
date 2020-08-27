<?php
namespace App\Http;

use Symfony\Component\HttpFoundation\{Request, JsonResponse};
use App\Service\{TjalFirstStepCrawler, TjalSecondStepCrawler};
use Symfony\Component\Routing\Annotation\Route;

final class PostTjal
{
    private $firstStepCrawler;
    private $secondStepCrawler;

    public function __construct(
        TjalFirstStepCrawler $firstStepCrawler,
        TjalSecondStepCrawler $secondStepCrawler
    ) {
        $this->firstStepCrawler  = $firstStepCrawler;
        $this->secondStepCrawler = $secondStepCrawler;
    }

    /**
     * @Route("/api/tjal", methods={"POST"})
     */
    public function __invoke(Request $request)
    {
        $content = json_decode($request->getContent(), true);

        if (!isset($content['processNumber'])) {
            return new JsonResponse(['The processNumber is required!'], 400);
        }

        $processNumber = trim($content['processNumber']);

        $schema = [
            'PrimeiroGrau' => $this->firstStepCrawler->filter($processNumber),
            'SegundoGrau'  => $this->secondStepCrawler->filter($processNumber),
        ];

        $response = new JsonResponse();
        $response->setJson(json_encode($schema, JSON_UNESCAPED_UNICODE));

        return $response;
    }
}