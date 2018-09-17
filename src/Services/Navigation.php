<?php

namespace App\Services;

use App\Entity\Beer;
use App\Entity\GeoCode;
use Doctrine\ORM\EntityManagerInterface;

class Navigation
{
    private $dataProvider;
    private $breweriesLocations = array();
    private $calculator;
    private $pathFinder;
    private $home;
    private $searchRange;

    public function __construct(
        GeoCode $home,
        $searchRangeKm,
        GeoCalculatorInterface $explorer,
        PathFinderInterface $pathFinder,
        DataProvider $provider
    )
    { //<-){ vienoj eilutej
        $this->dataProvider = $provider;
        $this->calculator = $explorer;
        $this->pathFinder = $pathFinder;
        $this->home = $home;
        $this->searchRange = $searchRangeKm;
        $this->findBreweriesCoordinates();
    }

    private function findBreweriesCoordinates()
    {
        unset($this->breweriesLocations);
        $this->breweriesLocations = array();
        $coordinates = $this->dataProvider->getLocations();

        foreach ($coordinates as $coordinate) {
            $distance = $this->calculator->getDistance($this->home, $coordinate);
            if ($this->searchRange >= $distance) {
                $this->breweriesLocations[] = $coordinate;
            }
        }

        unset($coordinates);
    }

    public function setNewHome(GeoCode $home)
    {
        $this->home = $home;
        $this->findBreweriesCoordinates();
    }

    public function setNewSearchRange(?double $rangeKm)
    {
        $this->searchRange = $rangeKm;
        $this->findBreweriesCoordinates();
    }

    public function getHome(): ?GeoCode
    {
        return $this->home;
    }

    public function getRange(): ?double
    {
        return $this->searchRange;
    }

    public function getBreweriesLocations(): ?array
    {
        return $this->breweriesLocations;
    }

    /**
     * Return path way of travel
     */
    public function findPath(): ?array
    {
        $beers = $this->dataProvider->getBeers();

        $path = $this->pathFinder->findPath($this->home, $this->breweriesLocations, $beers, $this->searchRange);

        return $path;

    }

}