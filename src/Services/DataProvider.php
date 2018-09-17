<?php

namespace App\Services;


use App\Entity\Beer;
use App\Entity\Brewery;
use App\Entity\GeoCode;
use Doctrine\ORM\EntityManagerInterface;

class DataProvider
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Return all beer from database
     * @return object[]
     */
    public function getBeers(): array
    {
        return $this->entityManager->getRepository(Beer::class)->findAll();
    }

    /**
     * Return all breweries from database
     * @return object[]
     */
    public function getBreweries(): array
    {
        return $this->entityManager->getRepository(Brewery::class)->findAll();
    }

    /**
     * Return all GeoCodes from database
     * @return object[]
     */
    public function getLocations(): array
    {
        return $this->entityManager->getRepository(GeoCode::class)->findAll();
    }

    /**
     * Return brewery from database by id
     * @param $id
     * @return null|Brewery
     */
    public function getBrewery($id)
    {
        return $this->entityManager->getRepository(Brewery::class)->find($id);
    }

    /**
     * Return GeoCode from database by id
     * @param $id
     * @return null|GeoCode
     */
    public function getLocation($id)
    {
        return $this->entityManager->getRepository(GeoCode::class)->find($id);
    }

    /**
     * Return beer from database by id
     * @param $id
     * @return null\Beer
     */
    public function getBeer($id)
    {
        return $this->entityManager->getRepository(Beer::class)->find($id);
    }

    /**
     * Return all beers from given breweries without duplication
     *
     * @param array $breweries
     * @return array
     */
    public function getBreweriesBeers(?array $breweries)
    {
        $collection = array();

        foreach ($breweries as $brewery) {
            if ($brewery instanceof Brewery) {

                $beers = $this->getBreweryBeers($brewery);

                foreach ($beers as $beer) {
                    if (!in_array($beer, $collection)) {
                        $collection[] = $beer;
                    }
                }
            }
        }

        return $collection;
    }

    /**
     * Return all beers from one brewery
     *
     * @param Brewery $brewery
     * @return array
     */
    public function getBreweryBeers(Brewery $brewery)
    {
        return $brewery->getBeers();
    }

}