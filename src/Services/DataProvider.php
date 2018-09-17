<?php

namespace App\Services;


use App\Entity\Beer;
use App\Entity\Brewery;
use App\Entity\GeoCode;
use Doctrine\ORM\EntityManagerInterface;

class DataProvider
{
    private $entityManager;
    private $beers;
    private $breweries;
    private $locations;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        $this->beers = $this->entityManager->getRepository(Beer::class)->findAll();
        $this->breweries = $this->entityManager->getRepository(Brewery::class)->findAll();
        $this->locations = $this->entityManager->getRepository(GeoCode::class)->findAll();
    }

    /**
     * @return object[]
     */
    public function getBeers(): array
    {
        return $this->beers;
    }

    /**
     * @return object[]
     */
    public function getBreweries(): array
    {
        return $this->breweries;
    }

    /**
     * @return object[]
     */
    public function getLocations(): array
    {
        return $this->locations;
    }

    /**
     * @param $id
     * @return null\Brewery
     */
    public function getBrewery($id)
    {
        foreach ($this->breweries as $brewery) {
            if ($brewery->getId() == $id) {
                return $brewery;
            }
        }

        return null;
    }

    /**
     * @param $id
     * @return null\GeoCode
     */
    public function getLocation($id)
    {
        foreach ($this->locations as $location) {
            if ($location->getId() == $id) {
                return $location;
            }
        }

        return null;
    }

    /**
     * @param $id
     * @return null\Beer
     */
    public function getBeer($id)
    {
        foreach ($this->beers as $beer) {
            if ($beer->getId() == $id) {
                return $beer;
            }
        }

        return null;
    }

    /**
     * Return all beers from one brewery
     *
     * @param Brewery $brewery
     * @return array
     */
    public function getBreweryBeers(Brewery $brewery)
    {
        $beers = array();
        foreach ($this->beers as $beer) {
            if ($beer->getBrewery() == $brewery) {
                $beers[] = $beer;
            }
        }

        return $beers;
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

}