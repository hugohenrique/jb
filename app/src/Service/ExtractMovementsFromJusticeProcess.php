<?php
namespace App\Service;

use Symfony\Component\DomCrawler\Crawler;

trait ExtractMovementsFromJusticeProcess
{
    public static function extractMovements(Crawler $crawler): array
    {
        $data = [];

        $reducedMovements = $crawler->reduce(function (Crawler $node) {
            return !empty($node->text());
        });

        $reducedMovements->each(function (Crawler $node) use (&$data) {
            $cell = explode(' ', $node->text(), 2);

            $data[] = [
                $cell[0] => $cell[1]
            ];
        });

        return $data;
    }
}