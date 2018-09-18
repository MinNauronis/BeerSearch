<?php

namespace App\Services\PathFinder;

use App\Entity\Brewery;
use App\Entity\GeoCode;
use App\Services\Calculator\GeoCalculatorInterface;

class AdvancedPathFinder implements PathFinderInterface
{
    /**
     * @var GeoCalculatorInterface
     */
    private $calculator;

    public function __construct(GeoCalculatorInterface $calculator)
    {
        $this->calculator = $calculator;
    }


    /**
     * Return travel way
     *
     * @param $home
     * @param $locations
     * @param $beers
     * @param $searchDistance
     * @return array
     */
    public function findPath($home, $locations, $beers, $searchDistance): array
    {
        $searchLimit = $searchDistance * 2;
        $assembly = $this->setup($locations, $beers);
        $path = array();
        $path[] = $home;
        $continueSearch = true;

        while ($continueSearch) {
            $id = $this->nextLocationId($assembly, $searchLimit, end($path), $home);

            if ($id === null) {
                $continueSearch = false;
                break;
            }

            $nextLocation = $assembly[$id][0];
            $consumedDistance = $this->calculator->getDistance(end($path), $nextLocation);

            $searchLimit -= $consumedDistance;
            $path[] = $nextLocation;

            $assembly = $this->wipeOutBeer($assembly, $id);
        }

        $path[] = $home;

        return $path;
    }

    /**
     * @param $locations
     * @param $beers
     * @return array|null
     */
    private function setup($locations, $beers): ?array
    {
        // $location => array($beer, $beer,... );
        $assembly = array();

        foreach ($locations as $location) {
            $brewery = $location->getBrewery();
            $collectedBeer = $this->collectBeers($brewery, $beers);
            array_push($assembly, array($location, $collectedBeer));
        }

        return $assembly;
    }

    /**
     * @param Brewery $brewery
     * @param $beers
     * @return array
     */
    private function collectBeers(Brewery $brewery, $beers)
    {
        $beerCollection = array();

        foreach ($beers as $beer) {
            if ($beer->getBrewery() == $brewery) {
                $beerCollection[] = $beer;
            }
        }

        return $beerCollection;
    }

    /**
     * Return id of next most valuable location.
     * Return null if not found.
     * @param $assembly
     * @param $leftDistance
     * @param GeoCode $lastLocation
     * @param GeoCode $home
     * @return int|null
     */
    private function nextLocationId($assembly, $leftDistance, GeoCode $lastLocation, GeoCode $home)
    {
        $id = null;
        $bestScore = 1e-6;
        $consumedDistance = $leftDistance + 1;

        for ($i = 0; $i < count($assembly); $i++) {
            $item = $assembly[$i];
            $distanceToNewLoc = $this->calculator->getDistance($item[0], $lastLocation);
            $distanceToHome = $this->calculator->getDistance($item[0], $home);
            $requiredDistance = $distanceToHome + $distanceToNewLoc;

            if ($requiredDistance <= $leftDistance) {
                $score = $this->scoreCalculator($distanceToNewLoc, count($item[1]));
                if ($score > $bestScore) {
                    $id = $i;
                    $bestScore = $score;
                    $consumedDistance = $requiredDistance;

                } elseif ($score === $bestScore && $consumedDistance > $requiredDistance) {
                    $id = $i;
                    $bestScore = $score;
                    $consumedDistance = $requiredDistance;
                }
            }
        }

        return $id;
    }

    private function scoreCalculator($distance, $beerCount)
    {

        if ($beerCount == 0) {
            return 0;
        }

        //almost free beer
        if ($distance == 0) {
            return 100;
        }
        //need better score's function
        //(would be nice integration of location of home.)
        return $beerCount * 100 / $distance;
    }

    /**
     * Remove collected beers from all assembly
     * @param $assembly
     * @param $id
     * @return array|null
     */
    private function wipeOutBeer($assembly, $id): ?array
    {
        $current = $assembly[$id];
        $takenBeers = $current[1];

        //remove duplicates in others breweries
        foreach ($assembly as $brewery) {

            //(do not delete current beer... yet)
            if ($current == $brewery) {
                continue;
            }

            for ($i = 0; $i < count($brewery); $i++) {
                $beer = $brewery[$i];

                if (in_array($beer, $takenBeers)) {
                    unset($brewery[$i]);
                    $i--;
                }
            }
        }

        //wipe out current beers
        $assembly[$id][1] = array();

        return $assembly;
    }


}
