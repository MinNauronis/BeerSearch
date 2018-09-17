<?php

namespace App\Services\Calculator;

use App\Entity\GeoCode;

interface GeoCalculatorInterface{
    /**
     * Edge of ellipse (circle) at NESW directions
     *
     * @param GeoCode $home Start location
     * @param $range in kilometres
     * @return array of Coordinate
     */
    public function getBoundariesPoints(GeoCode $home, $range): array;

    /**
     * Return distance between given points
     *
     * @param GeoCode $point1
     * @param GeoCode $point2
     * @return float
     */
    public function getDistance(GeoCode $point1, GeoCode $point2): float;

}