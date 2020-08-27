<?php
namespace App\Service;

use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class JusticeProcessCrawler
{
    use ExtractMovementsFromJusticeProcess;

    private $httpClient;
    private $httpBrowser;
    private $baseURL;

    public function __construct(HttpClientInterface $httpClient, string $baseURL)
    {
        $this->httpClient  = $httpClient;
        $this->httpBrowser = new HttpBrowser($httpClient);
        $this->baseURL     = $baseURL;
    }

    protected function prepareResponse(array $formValues): array
    {
        $crawler = $this->submitForm($formValues);

        $processDataRaw = $crawler->filter('table.secaoFormBody')
                                  ->eq(1)
                                  ->filter('.labelClass');

        $processParts = str_replace('&nbsp', ' ', $crawler->filter('#tablePartesPrincipais')->text(''));

        return array_filter(
            [
                'DadosDoProcesso'  => $this->extractProcessData($processDataRaw),
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
        $whitelist = [
            'Classe',
            'Área',
            'Assunto',
            'Distribuição',
            'Juiz',
            'Valor da ação',
        ];

        $data = [];

        $crawler->each(function (Crawler $node) use ($whitelist, &$data) {
            $rows = $node->closest('tr');

            $rows->each(function (Crawler $rowNode) use ($whitelist, &$data) {
                $cell = explode(':', $rowNode->text(), 2);

                if (in_array($cell[0], $whitelist, true)) {
                    $data[] = trim($cell[0].' - '.trim($cell[1]));
                }
            });
        });

        return $data;
    }
}