<?php

namespace App\Tests;

use App\Entity\GeoCode;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GeoCodeRepositoryTest extends KernelTestCase
{
    private $entityManager;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /*public function testGetPoints()
    {
        $points = $this->entityManager
            ->getRepository(GeoCode::class)
            ->getPoints(*pointsNeeded*);

        $this->assertNotEmpty($points, 'something is found');
    }*/

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; //avoid memory leaks
    }

}