<?php
namespace App\Tests\Api;

use App\Tests\ApiTester;

/**
 * @package App\Tests\Api
 * @group Tjal
 */
final class PostTjalCest
{
    /**
     * @test
     */
    public function tryGetTjalWithoutProcessNumber(ApiTester $I): void
    {
        $I->wantToTest('Try get process without process number I should receive 400 status code as body');

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/api/tjal', []);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['The processNumber is required!']);
    }

    public function getTjalProcessOnlyFirstDegree(ApiTester $I): void
    {
        $I->wantToTest('Try get a specific. This only first degree. I should receive 200 status code and more details');

        $processNumber = '0710802-55.2018.8.02.0001';

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/api/tjal', ['processNumber' => $processNumber]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseJsonMatchesJsonPath('$.PrimeiroGrau.DadosDoProcesso');
        $I->seeResponseJsonMatchesJsonPath('$.PrimeiroGrau.PartesDoProcesso');
        $I->seeResponseJsonMatchesJsonPath('$.PrimeiroGrau.Movimentacoes');
        $I->dontSeeResponseJsonMatchesJsonPath('$.SegundoGrau.DadosDoProcesso');
    }

    public function getTjalProcessOnlySecondDegree(ApiTester $I): void
    {
        $I->wantToTest('Try get a specific. This only second degree. I should receive 200 status code and more details');

        $processNumber = '0500505-05.2020.8.02.0000';

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/api/tjal', ['processNumber' => $processNumber]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseJsonMatchesJsonPath('$.SegundoGrau.DadosDoProcesso');
        $I->seeResponseJsonMatchesJsonPath('$.SegundoGrau.PartesDoProcesso');
        $I->seeResponseJsonMatchesJsonPath('$.SegundoGrau.Movimentacoes');
        $I->dontSeeResponseJsonMatchesJsonPath('$.PrimeiroGrau.DadosDoProcesso');
    }

    public function getTjalProcessFirstAndSecondDegree(ApiTester $I): void
    {
        $I->wantToTest('This process is first and second degree. I should receive 200 status code and more details');

        $processNumber = '0000653-42.2013.8.02.0025';

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/api/tjal', ['processNumber' => $processNumber]);
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
