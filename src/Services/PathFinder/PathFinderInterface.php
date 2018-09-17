<?php

namespace App\Services\PathFinder;

interface PathFinderInterface
{
    /**
     * Return travel way
     *
     * @param $home
     * @param $locations
     * @param $beers
     * @param $searchDistance
     * @return array
     */
    public function findPath($home, $locations, $beers, $searchDistance) : array;
}