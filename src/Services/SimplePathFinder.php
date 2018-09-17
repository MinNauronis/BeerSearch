<?php

namespace App\Services;

use App\Entity\GeoCode;

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
     * @param $currentLocation Last visited location
     * @param $selectedLocations All visited locations
     * @param $breweriesLocations All locations of breweries
     * @param $distance in kilometres
     * @return null\GeoCode
     */
    private function findNearestLocation($currentLocation, $selectedLocations, $breweriesLocations, $distance)
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