<?php
namespace App\Service;

use App\ProcessNumber;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TjmsFirstStepCrawler extends JusticeProcessCrawler
{
    public function __construct(HttpClientInterface $httpClient)
    {
        parent::__construct($httpClient, 'https://esaj.tjms.jus.br/cpopg5/open.do');
    }

    public function filter(string $processNumber): array
    {
        preg_match(ProcessNumber::REGEX, $processNumber, $processNumberParts);

        return $this->prepareResponse(
            [
                'conversationId'                         => '',
                'cbPesquisa'                             => 'NUMPROC',
                'dadosConsulta.localPesquisa.cdLocal'    => '1',
                'dadosConsulta.tipoNuProcesso'           => 'UNIFICADO',
                'dadosConsulta.valorConsulta'            => '',
                'dadosConsulta.valorConsultaNuUnificado' => $processNumber,
                'numeroDigitoAnoUnificado'               => $processNumberParts[1],
                'foroNumeroUnificado'                    => $processNumberParts[3],
                'uuidCaptcha'                            => '',
            ]
        );
    }
}