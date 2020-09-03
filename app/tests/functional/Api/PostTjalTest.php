<?php
namespace App\FunctionalTests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DomCrawler\Crawler;

final class PostTjalTest extends WebTestCase
{
    /**
     * @test
     */
    public function tryGetTjalWithoutProcessNumber(): void
    {
        $httpClient = self::createClient();

        $httpClient->request(
            'POST',
            '/api/tjal',
            [],
            [],
            ['Content-Type' => 'application/json']
        );

        $response = $httpClient->getResponse();

        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(400);
        $this->assertEquals(json_encode(['The processNumber is required!']), $response->getContent());
    }

    /**
     * @test
     */
    public function getTjalProcessOnlyFirstDegree(): void
    {
        $processNumber = '0710802-55.2018.8.02.0001';

        $httpClient = self::createClient();

        $httpClient->request(
            'POST',
            '/api/tjal',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['processNumber' => $processNumber])
        );

        $response = $httpClient->getResponse();

        $structuredContent = json_decode($response->getContent(), true);

        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(200);

        $this->assertTrue(isset($structuredContent['PrimeiroGrau']['DadosDoProcesso']));
        $this->assertTrue(isset($structuredContent['PrimeiroGrau']['PartesDoProcesso']));
        $this->assertTrue(isset($structuredContent['PrimeiroGrau']['Movimentacoes']));

        $this->assertFalse(isset($structuredContent['SegundoGrau']['DadosDoProcesso']));
    }

    /**
     * @test
     */
    public function getTjalProcessOnlySecondDegree(): void
    {
        $processNumber = '0500505-05.2020.8.02.0000';

        $httpClient = self::createClient();

        $httpClient->request(
            'POST',
            '/api/tjal',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['processNumber' => $processNumber])
        );

        $response = $httpClient->getResponse();

        $structuredContent = json_decode($response->getContent(), true);

        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(200);

        $this->assertTrue(isset($structuredContent['SegundoGrau']['DadosDoProcesso']));
        $this->assertTrue(isset($structuredContent['SegundoGrau']['PartesDoProcesso']));
        $this->assertTrue(isset($structuredContent['SegundoGrau']['Movimentacoes']));

        $this->assertFalse(isset($structuredContent['PrimeiroGrau']['DadosDoProcesso']));
    }

    /**
     * @test
     */
    public function getTjalProcessFirstAndSecondDegree(): void
    {
        $processNumber = '0000653-42.2013.8.02.0025';

        $httpClient = self::createClient();

        $httpClient->request(
            'POST',
            '/api/tjal',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['processNumber' => $processNumber])
        );

        $response = $httpClient->getResponse();

        $structuredContent = json_decode($response->getContent(), true);

        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(200);

        $this->assertTrue(isset($structuredContent['PrimeiroGrau']['DadosDoProcesso']));
        $this->assertTrue(isset($structuredContent['PrimeiroGrau']['PartesDoProcesso']));
        $this->assertTrue(isset($structuredContent['PrimeiroGrau']['Movimentacoes']));

        $this->assertTrue(isset($structuredContent['SegundoGrau']['DadosDoProcesso']));
        $this->assertTrue(isset($structuredContent['SegundoGrau']['PartesDoProcesso']));
        $this->assertTrue(isset($structuredContent['SegundoGrau']['Movimentacoes']));
    }
}
