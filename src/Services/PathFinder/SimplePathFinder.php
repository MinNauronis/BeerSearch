<?php

namespace App\Services\PathFinder;

use App\Entity\GeoCode;
use App\Services\Calculator\GeoCalculatorInterface;

class SimplePathFinder implements PathFinderInterface
{
    private $calculator;

    public function __construct(GeoCalculatorInterface $calculator)
    {
        $this->calculator = $calculator;
    }

    /**
     * Return travel way.
     * This method do not use $beers
     *
     * @param $home
     * @param $locations
     * @param $beers
     * @param $searchDistance
     * @return array
     */
    public function findPath($home, $locations, $beers = null, $searchDistance): array
    {
        //Path should start and end the same location.
        $path = array($home);
        $continueSearch = true;

        while ($continueSearch) {
            $nextLocation = $this->findNearestLocation(end($path), $path, $locations, $searchDistance);

            if ($nextLocation) {
                $searchDistance -= $this->calculator->getDistance(end($path), $nextLocation);

                if ($searchDistance <= 0) {
                    $continueSearch = false;
                    break;
                } else {
                    $path[] = $nextLocation;
                }

            } else {
                $continueSearch = false;
                break;
            }
        }

        $path[] = $home;

        return $path;
    }

    /**
     * Return nearest location, which is not selected yet
     *
     * $currentLocation - Last visited location
     * $selectedLocations - All visited locations
     * $breweriesLocations - All locations of breweries
     * $distance in kilometres
     *
     * @param GeoCode $currentLocation
     * @param $selectedLocations
     * @param $breweriesLocations
     * @param $distance
     * @return null|GeoCode
     */
    private function findNearestLocation($currentLocation, $selectedLocations, $breweriesLocations, $distance): GeoCode
    {
        // pasirinkta vieta iš $breweriesLocations galėtų būti pašalinta iš masyvo
        // count = 0 -> return;
        if (!count($breweriesLocations)) {
            return null;
        }

        $chosen = null;

        foreach ($breweriesLocations as $brewery) {
            $tempDistance = $this->calculator->getDistance($currentLocation, $brewery);

            if ($tempDistance < $distance)
                //if already selected, skip
                if (!in_array($brewery, $selectedLocations)) {
                    $distance = $tempDistance;
                    $chosen = $brewery;
                }
        }

        return $chosen;
    }
}
