<?php

namespace App\Services;


use App\Entity\Beer;
use App\Entity\Brewery;
use App\Entity\GeoCode;

class DataFormater
{
    private $data;
    private $calculator;
    private $movesDirections = ['o', '->', '<-'];

    public function __construct(DataProvider $data, GeoCalculatorInterface $calculator)
    {
        $this->calculator = $calculator;
        $this->data = $data;
    }

    /**
     *
     * @param array|null $path
     * @return array
     */
    public function getTravelReport(?array $path)
    {
        $report = array();
        //first and last should be location of home.
        $header = $this->formatTravelTitle(count($path) - 2);

        $report += array('header' => $header);
        $report += array('body' => $this->formatTravelBody($path));
        $report += array('totalDistance' => $this->formatTotalDistanceLine($path));

        return $report;
    }

    private function formatTravelTitle($locationsCount)
    {
        return 'Found ' . $locationsCount . '   breweries:';
    }

    private function formatTravelBody(?array $path)
    {
        $body = array();
        $prevLocation = null;

        $counter = count($path);
        for ($i = 0; $i < $counter; $i++) {
            $isLast = $i == $counter - 1 ? true : false;
            $currentLocation = $path[$i];
            $line = $this->formatTravelBodyLine($currentLocation, $prevLocation, $isLast);
            $prevLocation = $currentLocation;

            $body[] = $line;
        }

        return $body;
    }

    /**
     *
     * @param GeoCode $currentLocation
     * @param null\GeoCode $prevLocation
     * @param bool $isLast
     * @return array
     */
    private function formatTravelBodyLine($currentLocation, $prevLocation = null, $isLast = false)
    {
        // line = ['direction' => , 'title' => '[111] {brewery name}', 'coordinates' => '122.225 122.444', 'distance' => '1' ]
        $direction = $this->movesDirections[1];

        if ($isLast) {
            $direction = $this->movesDirections[2];
        }

        if ($prevLocation == null) {
            $direction = $this->movesDirections[0];
        }

        $title = $this->formatBreweryTitle($currentLocation->getBrewery());
        $coordinates = $this->formatCoordinates($currentLocation);
        $distance = $this->formatDistance($currentLocation, $prevLocation);
        $line = array('direction' => $direction, 'title' => $title, 'coordinates' => $coordinates, 'distance' => $distance);
        return $line;


    }

    private function formatBreweryTitle(?Brewery $brewery): ?string
    {
        if (!$brewery instanceof Brewery) {
            //only home should not to have brewery
            return 'HOME:';
        }

        if ($brewery->getId()) {
            return '[' . $brewery->getId() . '] ' . $brewery->getName() . ':';
        }

        return ' can not to identify brewery ';

    }

    private function formatCoordinates(GeoCode $code): ?string
    {
        return round($code->getLatitude(), 8) . ' ' . round($code->getLongitude(), 8);
    }

    private function formatDistance(?GeoCode $point1, ?GeoCode $point2): ?string
    {
        if ($point2 == null || $point1 == null)
            return 'distance 0km';

        $distance = $this->calculator->getDistance($point1, $point2);
        return 'distance ' . round($distance) . 'km';
    }

    private function formatTotalDistanceLine(?array $path)
    {
        $totalDistance = 0;
        for ($i = 1; $i < count($path); $i++) {
            $distance = $this->calculator->getDistance($path[$i - 1], $path[$i]);
            $totalDistance += $distance;
        }

        return 'Total travel distance is ' . round($totalDistance) . 'km';
    }

    public function getBeersReport(?array $path)
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