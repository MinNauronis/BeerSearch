<?php

namespace App\Services\DataFormatter;

use App\Entity\Brewery;
use App\Entity\GeoCode;
use App\Services\Calculator\GeoCalculatorInterface;

class TravelFormatter
{
    private $movesDirections = ['o', '->', '<-'];
    private $calculator;

    public function __construct(GeoCalculatorInterface $calculator)
    {
        $this->calculator = $calculator;
    }

    /**
     * Prepare data to print
     * @param array|null $path
     * @return array
     */
    public function getReport(?array $path): array
    {
        $report = array();
        //first and last should be location of home.
        $header = $this->formatTravelTitle(count($path) - 2);

        $report += array('header' => $header);
        $report += array('body' => $this->formatTravelBody($path));
        $report += array('totalDistance' => $this->formatTotalDistanceLine($path));

        return $report;
    }

    private function formatTravelTitle($locationsCount): string
    {
        return 'Found ' . $locationsCount . ' breweries:';
    }

    private function formatTravelBody(?array $path): array
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
     * @param null|GeoCode $prevLocation
     * @param bool $isLast
     * @return array
     */
    private function formatTravelBodyLine($currentLocation, $prevLocation = null, $isLast = false): array
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

    private function formatBreweryTitle(?Brewery $brewery): string
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

    private function formatCoordinates(GeoCode $code): string
    {
        return round($code->getLatitude(), 8) . ' ' . round($code->getLongitude(), 8);
    }

    private function formatDistance(?GeoCode $point1, ?GeoCode $point2): string
    {
        if ($point2 == null || $point1 == null)
            return 'distance 0km';

        $distance = $this->calculator->getDistance($point1, $point2);
        return 'distance ' . round($distance) . 'km';
    }

    private function formatTotalDistanceLine(?array $path): string
    {
        $totalDistance = 0;
        for ($i = 1; $i < count($path); $i++) {
            $distance = $this->calculator->getDistance($path[$i - 1], $path[$i]);
            $totalDistance += $distance;
        }

        return 'Total travel distance is ' . round($totalDistance) . 'km';
    }

}
