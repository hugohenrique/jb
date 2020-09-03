<?php
namespace App\Service;

use App\ProcessNumber;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TjmsSecondStepCrawler extends TjmsCrawler
{
    public function __construct(HttpClientInterface $httpClient)
    {
        parent::__construct($httpClient, 'https://esaj.tjms.jus.br/cposg5/open.do');
    }

    public function filter(string $processNumber): array
    {
        preg_match(ProcessNumber::REGEX, $processNumber, $processNumberParts);

        return $this->prepareResponse(
            [
                'conversationId'              => '',
                'paginaConsulta'              => '0',
                'cbPesquisa'                  => 'NUMPROC',
                'numeroDigitoAnoUnificado'    => $processNumberParts[1],
                'foroNumeroUnificado'         => $processNumberParts[3],
                'dePesquisaNuUnificado'       => $processNumber,
                'dePesquisa'                  => '',
                'tipoNuProcesso'              => 'UNIFICADO',
            ]
        );
    }
}