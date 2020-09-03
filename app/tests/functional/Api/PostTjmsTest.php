<?php
namespace App\FunctionalTests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DomCrawler\Crawler;

final class PostTjmsTest extends WebTestCase
{
    /**
     * @test
     */
    public function tryGetTjalWithoutProcessNumber(): void
    {
        $httpClient = self::createClient();

        $httpClient->request(
            'POST',
            '/api/tjms',
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
        $processNumber = '0017658-49.2008.8.12.0001';

        $httpClient = self::createClient();

        $httpClient->request(
            'POST',
            '/api/tjms',
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
    public function getTjalProcessSecondDegree(): void
    {
        $processNumber = '0800110-93.2019.8.12.0032';

        $httpClient = self::createClient();

        $httpClient->request(
            'POST',
            '/api/tjms',
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
    }

    /**
     * @test
     */
    public function getTjalProcessFirstAndSecondDegree(): void
    {
        $processNumber = '0821901-51.2018.8.12.0001';

        $httpClient = self::createClient();

        $httpClient->request(
            'POST',
            '/api/tjms',
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
