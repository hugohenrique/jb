<?php
namespace App\Tests\Api;

use App\Tests\ApiTester;

/**
 * @package App\Tests\Api
 * @group Tjal
 */
final class PostTjmsCest
{
    /**
     * @test
     */
    public function tryGetProcessWithoutNumber(ApiTester $I): void
    {
        $I->wantToTest('Try get process without process number I should receive 400 status code as body');

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/api/tjms', []);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['The processNumber is required!']);
    }

    public function getProcessOnlyFirstDegree(ApiTester $I): void
    {
        $I->wantToTest('Try get a specific. This only first degree. I should receive 200 status code and more details');

        $processNumber = '0017658-49.2008.8.12.0001';

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/api/tjms', ['processNumber' => $processNumber]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseJsonMatchesJsonPath('$.PrimeiroGrau.DadosDoProcesso');
        $I->seeResponseJsonMatchesJsonPath('$.PrimeiroGrau.PartesDoProcesso');
        $I->seeResponseJsonMatchesJsonPath('$.PrimeiroGrau.Movimentacoes');

        $I->dontSeeResponseJsonMatchesJsonPath('$.SegundoGrau.DadosDoProcesso');
    }

    public function getProcessOnlySecondDegree(ApiTester $I): void
    {
        $I->wantToTest('Try get a specific. This only second degree. I should receive 200 status code and more details');

        $processNumber = '0800110-93.2019.8.12.0032';

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/api/tjms', ['processNumber' => $processNumber]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseJsonMatchesJsonPath('$.SegundoGrau.DadosDoProcesso');
        $I->seeResponseJsonMatchesJsonPath('$.SegundoGrau.PartesDoProcesso');
        $I->seeResponseJsonMatchesJsonPath('$.SegundoGrau.Movimentacoes');
    }

    public function getProcessFirstAndSecondDegree(ApiTester $I): void
    {
        $I->wantToTest('This process is first and second degree. I should receive 200 status code and more details');

        $processNumber = '0821901-51.2018.8.12.0001';

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/api/tjms', ['processNumber' => $processNumber]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseJsonMatchesJsonPath('$.PrimeiroGrau.DadosDoProcesso');
        $I->seeResponseJsonMatchesJsonPath('$.PrimeiroGrau.PartesDoProcesso');
        $I->seeResponseJsonMatchesJsonPath('$.PrimeiroGrau.Movimentacoes');

        $I->seeResponseJsonMatchesJsonPath('$.SegundoGrau.DadosDoProcesso');
        $I->seeResponseJsonMatchesJsonPath('$.SegundoGrau.PartesDoProcesso');
        $I->seeResponseJsonMatchesJsonPath('$.SegundoGrau.Movimentacoes');
    }
}
