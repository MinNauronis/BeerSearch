<?php

namespace App\Services\DataFormatter;

use App\Entity\Beer;
use App\Entity\GeoCode;
use App\Services\Calculator\GeoCalculatorInterface;
use App\Services\DataProvider;

class BeerFormatter
{
    private $data;
    private $calculator;

    public function __construct(DataProvider $data, GeoCalculatorInterface $calculator)
    {
        $this->calculator = $calculator;
        $this->data = $data;
    }


    /**
     * @param array|null $path
     * @return array
     */
    public function getReport(?array $path)
    {
        $report = [];
        $breweries = [];

        foreach ($path as $location) {
            if ($location instanceof GeoCode) {
                $breweries[] = $location->getBrewery();
            }
        }

        $beers = $this->data->getBreweriesBeers($breweries);

        $report += array('header' => $this->formatBeersTitle(count($beers)));
        $report += array('body' => $this->formatBeersBody($beers));

        return $report;
    }

    private function formatBeersTitle($beersCounter)
    {
        return 'Collected ' . $beersCounter . ' beer types:';
    }

    private function formatBeersBody($beers)
    {
        $body = [];

        foreach ($beers as $beer) {
            if ($beer instanceof Beer) {
                $body[] = $this->formatBeerBodyLine($beer);
            }
        }

        return $body;
    }

    private function formatBeerBodyLine(Beer $beer)
    {
        return '-> ' . $beer->getName();
    }
}
