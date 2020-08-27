<?php
namespace App\Service;

use App\ProcessNumber;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TjmsSecondStepCrawler
{
    use ExtractMovementsFromJusticeProcess;

    private $httpBrowser;
    private $baseURL;

    public function __construct(HttpClientInterface $httpClient, string $baseURL)
    {
        $this->httpBrowser = new HttpBrowser($httpClient);
        $this->baseURL     = $baseURL;
    }

    public function filter(string $processNumber): array
    {
        preg_match(ProcessNumber::REGEX, $processNumber, $processNumberParts);

        return $this->prepareResponse(
            [
                'conversationId'           => '',
                'paginaConsulta'           => '0',
                'tipoNuProcesso'           => 'UNIFICADO',
                'cbPesquisa'               => 'NUMPROC',
                'dePesquisaNuUnificado'    => 'UNIFICADO',
                'dePesquisa'               => '',
                'dePesquisaNuUnificado'    => $processNumber,
                'numeroDigitoAnoUnificado' => $processNumberParts[1],
                'foroNumeroUnificado'      => $processNumberParts[3],
            ]
        );
    }

    protected function prepareResponse(array $formValues): array
    {
        $crawler = $this->submitForm($formValues);

        $processParts = str_replace('&nbsp', ' ', $crawler->filter('#tablePartesPrincipais')->text(''));

        return array_filter(
            [
                'DadosDoProcesso'  => $this->extractProcessData($crawler),
                'PartesDoProcesso' => trim($processParts),
                'Movimentacoes'    => $this->extractMovements($crawler->filter('#tabelaUltimasMovimentacoes tr'))
            ]
        );
    }

    private function submitForm(array $formValues): Crawler
    {
        $crawler = $this->httpBrowser->request('GET', $this->baseURL);

        $form = $crawler->filter('[name="consultarProcessoForm"]')->form();

        $form->setValues($formValues);

        return $this->httpBrowser->submit($form);
    }

    private function extractProcessData(Crawler $crawler): array
    {
        $headerSummary = $crawler->filter('div.unj-entity-header__summary .row')->eq(1);
        $headerDetails = $crawler->filter('#maisDetalhes');

        $whitelist = [
            'Classe',
            'Área',
            'Assunto',
            'Distribuição',
            'Juiz',
            'Valor da ação',
        ];

        $data = [];

        $headerSummary->filter('[class^="col-md-"]')->each(function (Crawler $node) use ($whitelist, &$data) {
            $label = $node->filter('.unj-label')->text();

            if (in_array($label, $whitelist)) {
                $data[] = $label.' - '.$node->filter('.lh-1-1')->text();
            }
        });

        $headerDetails->filter('[class^="col-lg-"]')->each(function (Crawler $node) use ($whitelist, &$data) {
            $label = $node->filter('.unj-label')->text();

            if (in_array($label, $whitelist)) {
                $data[] = $label.' - '.$node->filter('.line-clamp__2')->text();
            }
        });

        return $data;
    }
}