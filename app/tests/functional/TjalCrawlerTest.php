<?php
namespace App\FunctionalTests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DomCrawler\Crawler;

class TjalCrawlerTest extends WebTestCase
{
    private $httpBrowser;

    public function setUp(): void
    {
        $this->httpBrowser = new HttpBrowser(HttpClient::create());
    }

    public function testFirstStepCrawler(): void
    {
        $this->submitForm(
            'https://www2.tjal.jus.br/cpopg/open.do',
            [
                'conversationId'                         => '',
                'cbPesquisa'                             => 'NUMPROC',
                'dadosConsulta.localPesquisa.cdLocal'    => '1',
                'dadosConsulta.tipoNuProcesso'           => 'UNIFICADO',
                'dadosConsulta.valorConsulta'            => '',
                'dadosConsulta.valorConsultaNuUnificado' => '0710802-55.2018.8.02.0001',
                'numeroDigitoAnoUnificado'               => '0710802-55.2018',
                'foroNumeroUnificado'                    => '0001',
                'uuidCaptcha'                            => '',
            ]
        );
    }

    public function testSecondStepCrawler(): void
    {
        $this->submitForm(
            'https://www2.tjal.jus.br/cposg5/open.do',
            [
                'conversationId'           => '',
                'paginaConsulta'           => '1',
                'tipoNuProcesso'           => 'UNIFICADO',
                'dePesquisaNuUnificado'    => '0500505-05.2020.8.02.0000',
                'numeroDigitoAnoUnificado' => '0500505-05.2020',
                'foroNumeroUnificado'      => '0000',
                'dePesquisa'               => '',
                'uuidCaptcha'              => '',
            ]
        );
    }

    private function submitForm(string $url, array $formValues): void
    {
        $crawler = $this->httpBrowser->request('GET', $url);

        $this->assertGreaterThan(0, $crawler->filter('[name="consultarProcessoForm"]')->count());

        $form = $crawler->filter('[name="consultarProcessoForm"]')->form();

        $form->setValues($formValues);

        $crawler = $this->httpBrowser->submit($form);

        $this->assertGreaterThan(
            0,
            $crawler->filter('table.secaoFormBody')->eq(1)->filter('.labelClass')->count()
        );

        $this->assertGreaterThan(
            0,
            $crawler->filter('#tablePartesPrincipais')->count()
        );

        $this->assertGreaterThan(
            1,
            $crawler->filter('#tabelaUltimasMovimentacoes tr')->count()
        );
    }
}
