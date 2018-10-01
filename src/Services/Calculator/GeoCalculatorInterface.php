<?php

namespace App\Services\Calculator;

use App\Entity\GeoCode;

interface GeoCalculatorInterface
{
    /**
     * Edge of ellipse (circle) at NESW directions
     *
     * @param GeoCode $home Start location
     * @param double $range
     * @return array of Coordinate
     */
    public function getBoundariesPoints(GeoCode $home, float $range): array;

    /**
     * Return distance between given points
     *
     * @param GeoCode $point1
     * @param GeoCode $point2
     * @return float
     */
    public function getDistance(GeoCode $point1, GeoCode $point2): float;

}
