<?php

namespace App\Tests;

use App\Services\Coordinate;
use App\Services\GeoCalculator;
use PHPUnit\Framework\TestCase;

class CoordinatesOperatorTest extends TestCase
{
    public function testGetDistance()
    {
        $explorer = new GeoCalculator();
        $point1 = new Coordinate(1.0, 1.0);
        $point2 = new Coordinate(1.0, 11.0);

        $result = $explorer->getDistance($point1, $point2);

        $this->assertThat($result, $this->logicalAnd(
            $this->greaterThan(1111),
            $this->lessThan(1113)
        ));
    }

    public function testGetDistance2()
    {
        $explorer = new GeoCalculator();
        $point1 = new Coordinate(2.566, 157);
        $point2 = new Coordinate(54, -122.1449);

        $result = $explorer->getDistance($point1, $point2);

        $this->assertThat($result, $this->logicalAnd(
            $this->greaterThan(9179),
            $this->lessThan(9181)
        ));
    }

    public function testGetDistance3()
    {
        $explorer = new GeoCalculator();
        $point1 = new Coordinate(-1224524, 1);
        $point2 = new Coordinate(45, -114);

        $result = $explorer->getDistance($point1, $point2);

        $this->assertThat($result, $this->logicalAnd(
            $this->greaterThan(9417),
            $this->lessThan(9419)
        ));
    }

    /*public function testNewPoint()
    {
        $operator = new LocationExplorer();
        $point = new Coordinate(0.0, 0.0);
        $distance = 1000.0;
        $bearing = 90;

        $result = $operator->newPoint($point, $distance, $bearing);
        $expect = new Coordinate(0.00000000, 8.99333333);

        $this->assertThat($result->getLatitude(), $this->logicalAnd(
            $this->greaterThan($expect->getLatitude() - 0.0005),
            $this->lessThan($expect->getLatitude() + 0.0005)
        ));

        $this->assertThat($result->getLongitude(), $this->logicalAnd(
            $this->greaterThan($expect->getLongitude() - 0.0005),
            $this->lessThan($expect->getLongitude() + 0.0005)
        ));
    }*/

    /*public function testNewPoint2()
    {
        $operator = new LocationExplorer();
        $point = new Coordinate(1.0, 2.4);
        $distance = 1548.177;
        $bearing = -21;

        $result = $operator->newPoint($point, $distance, $bearing);
        $expect = new Coordinate(13.9775, -2.69805556);

        $this->assertThat($result->getLatitude(), $this->logicalAnd(
            $this->greaterThan($expect->getLatitude() - 0.0005),
            $this->lessThan($expect->getLatitude() + 0.0005)
        ));

        $this->assertThat($result->getLongitude(), $this->logicalAnd(
            $this->greaterThan($expect->getLongitude() - 0.0005),
            $this->lessThan($expect->getLongitude() + 0.0005)
        ));
    }*/
}