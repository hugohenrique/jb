<?php
namespace App\FunctionalTests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DomCrawler\Crawler;

class TjmsCrawlerTest extends WebTestCase
{
    private $httpBrowser;

    public function setUp(): void
    {
        $this->httpBrowser = new HttpBrowser(HttpClient::create());
    }

    public function testFirstStepCrawler(): void
    {
        $crawler = $this->httpBrowser->request('GET', 'https://esaj.tjms.jus.br/cpopg5/open.do');

        $this->assertGreaterThan(
            0,
            $crawler->filter('[name="consultarProcessoForm"]')->count()
        );

        $form = $crawler->filter('[name="consultarProcessoForm"]')->form();

        $form->setValues(
            [
                'uuidCaptcha'                            => '',
                'conversationId'                         => '',
                'cbPesquisa'                             => 'NUMPROC',
                'dadosConsulta.valorConsulta'            => '',
                'dadosConsulta.tipoNuProcesso'           => 'UNIFICADO',
                'dadosConsulta.valorConsultaNuUnificado' => '0800110-93.2019.8.12.0032',
                'numeroDigitoAnoUnificado'               => '0800110-93.2019',
                'foroNumeroUnificado'                    => '0032',
            ]
        );

        $crawler = $this->httpBrowser->submit($form);

        $this->assertGreaterThan(
            0,
            $crawler->filter('.unj-entity-header__summary .row')->eq(1)->count()
        );

        $this->assertGreaterThan(
            0,
            $crawler->filter('#maisDetalhes')->count()
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

    public function testSecondStepCrawler(): void
    {
        $crawler = $this->httpBrowser->request('GET', 'https://esaj.tjms.jus.br/cposg5/open.do');

        $this->assertGreaterThan(
            0,
            $crawler->filter('[name="consultarProcessoForm"]')->count()
        );

        $form = $crawler->filter('[name="consultarProcessoForm"]')->form();

        $form->setValues(
            [
                'conversationId'              => '',
                'paginaConsulta'              => '0',
                'cbPesquisa'                  => 'NUMPROC',
                'numeroDigitoAnoUnificado'    => '0800110-93.2019',
                'foroNumeroUnificado'         => '0032',
                'dePesquisaNuUnificado'       => '0800110-93.2019.8.12.0032',
                'dePesquisa'                  => '',
                'tipoNuProcesso'              => 'UNIFICADO',
            ]
        );

        $crawler = $this->httpBrowser->submit($form);

        $this->assertGreaterThan(
            0,
            $crawler->filter('.unj-entity-header__summary .row')->eq(1)->count()
        );

        $this->assertGreaterThan(
            0,
            $crawler->filter('#maisDetalhes')->count()
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
