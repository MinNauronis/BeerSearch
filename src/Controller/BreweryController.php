<?php

namespace App\Controller;

use App\Entity\GeoCode;
use App\Form\BeersFinderType;
use App\Services\DataFormatter\BeerFormatter;
use App\Services\DataFormatter\TravelFormatter;
use App\Services\DataProvider;
use App\Services\Calculator\GeoCalculator;
use App\Services\Navigation;
use App\Services\PathFinder\SimplePathFinder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BreweryController extends AbstractController
{
    /**
     * @Route("/brewery", name="brewery")
     */
    public function index()
    {
        return $this->render('brewery/index.html.twig', [
            'controller_name' => 'BreweryController',
        ]);
    }

    /**
     * @Route("/finder", name="beers_finder", methods="GET|POST")
     */
    public function beerFinder()
    {
        return $this->render('brewery/finder.html.twig', [
            'controller_name' => 'BreweryController',
        ]);
    }

    /**
     * @Route("/find", name="test", methods="GET|POST")
     */
    public function testBeerFinder(Request $request)
    {
        $form = $this->createForm(BeersFinderType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $startLocation = $form->getData();
            $home = $this->getHome($startLocation['latitude'], $startLocation['longitude']);
            $fuelDistance = 2000;


            $navi = $this->getNavigation($home, $fuelDistance);
            $path = $navi->findPath();
            $travelReport = $this->getTravelReport($path);
            $beerReport = $this->getBeerReport($path);

            dump($navi);
            dump($path);
            dump($travelReport);
            dump($beerReport);

            return $this->render('brewery/finder.html.twig', [
                'controller_name' => 'BreweryController',
                'form' => $form->createView(),
                'travelRoute' => $travelReport,
                'beersCollection' => $beerReport
            ]);

        }

        return $this->render('brewery/finder.html.twig', [
            'controller_name' => 'BreweryController',
            'form' => $form->createView(),
        ]);
    }

    private function getNavigation(GeoCode $home, $fuelDistance)
    {
        $provider = new DataProvider($this->getDoctrine()->getManager());
        $calculator = new GeoCalculator();
        $finder = new SimplePathFinder($calculator);
        $distanceKm = $fuelDistance / 2;
        $navi = new Navigation($home, $distanceKm, $calculator, $finder, $provider);
        return $navi;
    }

    private function getHome($latitude, $longitude) {
        $home = new GeoCode();
        $home->setLatitude(floatval($latitude));
        $home->setLongitude(floatval($longitude));
        return $home;
    }

    private function getTravelReport($path) {
        $calculator = new GeoCalculator();
        $formatter = new TravelFormatter($calculator);

        return $formatter->getReport($path);
    }

    private function getBeerReport($path) {
        $provider = new DataProvider($this->getDoctrine()->getManager());
        $calculator = new GeoCalculator();
        $formatter = new BeerFormatter($provider, $calculator);

        return $formatter->getReport($path);
    }
}
