<?php

namespace App\Services\DataFormatter;

use App\Entity\Beer;
use App\Entity\Brewery;
use App\Entity\GeoCode;
use App\Services\Calculator\GeoCalculatorInterface;
use Doctrine\ORM\EntityManagerInterface;

class BeerFormatter
{
    private $calculator;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, GeoCalculatorInterface $calculator)
    {
        $this->calculator = $calculator;
        $this->entityManager = $entityManager;
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

        $beers = $this->getBreweriesBeers($breweries);

        $report += array('header' => $this->formatBeersTitle(count($beers)));
        $report += array('body' => $this->formatBeersBody($beers));

        return $report;
    }


    /**
     * Return all beers from given breweries without duplication
     *
     * @param array $breweries
     * @return array
     */
    public function getBreweriesBeers(?array $breweries)
    {
        $collection = array();

        foreach ($breweries as $brewery) {

            if ($brewery instanceof Brewery) {
                $beers = $brewery->getBeers();

                foreach ($beers as $beer) {
                    if (!in_array($beer, $collection)) {
                        $collection[] = $beer;
                    }
                }
            }
        }

        return $collection;
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
