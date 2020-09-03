<?php
namespace App\Service;

use App\ProcessNumber;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TjalSecondStepCrawler extends TjalCrawler
{
    public function __construct(HttpClientInterface $httpClient)
    {
        parent::__construct($httpClient, 'https://www2.tjal.jus.br/cposg5/open.do');
    }

    public function filter(string $processNumber): array
    {
        preg_match(ProcessNumber::REGEX, $processNumber, $processNumberParts);

        return $this->prepareResponse(
            [
                'conversationId'           => '',
                'paginaConsulta'           => '1',
                'tipoNuProcesso'           => 'UNIFICADO',
                'numeroDigitoAnoUnificado' => $processNumberParts[1],
                'foroNumeroUnificado'      => $processNumberParts[3],
                'dePesquisaNuUnificado'    => $processNumber,
                'dePesquisa'               => '',
                'uuidCaptcha'              => '',
            ]
        );
    }
}