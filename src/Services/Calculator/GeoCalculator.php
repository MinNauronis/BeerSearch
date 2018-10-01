<?php

namespace App\Services\Calculator;

use App\Entity\GeoCode;

//https://www.wearedevelopers.com/define-or-const/
//"Const can not be defined anywhere except the outer-most context."
const R = 6371e3; // metres
//https://www.movable-type.co.uk/scripts/latlong.html
class GeoCalculator implements GeoCalculatorInterface
{
    /**
     * Edge of ellipse (circle) at NESW directions
     * range given in kilometres
     *
     * @param GeoCode $home
     * @param $range
     * @return array of Coordinate
     */
    public function getBoundariesPoints(GeoCode $home, $range): array
    {
        $directions = [];
        $dirNames = ['north', 'east', 'south', 'west'];

        for ($i = 0; $i < 4; $i++) {
            $bearing = 90 * $i;
            $direction = $this->newPoint($home, $range, $bearing);
            $directions += array($dirNames[$i] => $direction);
        }

        return $directions;
    }

    /**
     * Return new point by given distance and bearing.
     * Distance in kilometres.
     * Bearing in degree
     *
     * @param GeoCode $currentPoint
     * @param $distance
     * @param $bearing
     * @return GeoCode
     */
    private function newPoint(GeoCode $currentPoint, $distance, $bearing)
    {
        $phi1 = $currentPoint->getLatitude() * M_PI / 180;
        $lam1 = $currentPoint->getLongitude() * M_PI / 180;
        $bearingRadians = $bearing * M_PI / 180;
        $distance *= 1000;

        //φ2 = asin( sin φ1 ⋅ cos δ + cos φ1 ⋅ sin δ ⋅ cos θ ) LAT
        //λ2 = λ1 + atan2( sin θ ⋅ sin δ ⋅ cos φ1, cos δ − sin φ1 ⋅ sin φ2 ) LONG
        //δ is the angular distance d/R
        //θ is the bearing (clockwise from north)

        $phi2 = asin(sin($phi1) * cos($distance / R)
            + cos($phi1) * sin($distance / R) * cos($bearingRadians));

        $lam2 = $lam1 + atan2(
                sin($bearingRadians) * sin($distance / R) * cos($phi1),
                cos($distance / R) - sin($phi1) * sin($phi2));

        $latitude2 = round(rad2deg($phi2), 8);
        $longitude2 = round(rad2deg($lam2), 8);

        $result = new GeoCode();
        $result->setLatitude($latitude2);
        $result->setLongitude($longitude2);
        return $result;

    }

    /**
     * Return distance between given points
     * Haversine formula used
     *
     * @param GeoCode $point1
     * @param GeoCode $point2
     * @return float
     */
    public function getDistance(GeoCode $point1, GeoCode $point2): float
    {
        //latitudes as radians
        $phi1 = $point1->getLatitude() * M_PI / 180;
        $phi2 = $point2->getLatitude() * M_PI / 180;

        $deltaPhi = (($point2->getLatitude() - $point1->getLatitude()) * M_PI / 180);
        $deltaLam = (($point2->getLongitude() - $point1->getLongitude()) * M_PI / 180);

        /*Haversine formula:
        	a = sin²(Δφ/2) + cos φ1 ⋅ cos φ2 ⋅ sin²(Δλ/2)
            c = 2 ⋅ atan2( √a, √(1−a) )
            d = R ⋅ c
        */

        $a = pow(sin($deltaPhi / 2), 2) + cos($phi1) * cos($phi2) * pow(sin($deltaLam / 2), 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $metres = R * $c;
        $km = $metres / 1000;

        return round($km, 3);
    }
}
