<?php

namespace App\Controller;

use App\Entity\GeoCode;
use App\Form\BeersFinderType;
use App\Services\DataFormater;
use App\Services\DataProvider;
use App\Services\GeoCalculator;
use App\Services\Navigation;
use App\Services\SimplePathFinder;
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

            $provider = new DataProvider($this->getDoctrine()->getManager());
            $formatter = new DataFormater($provider, new GeoCalculator());

            $navi = $this->getNavigation($home, $fuelDistance, $provider);
            $path = $navi->findPath();
            $report1 = $formatter->getTravelReport($path);
            $report2 = $formatter->getBeersReport($path);

            dump($navi);
            dump($path);
            dump($report1);
            dump($report2);

            return $this->render('brewery/finder.html.twig', [
                'controller_name' => 'BreweryController',
                'form' => $form->createView(),
                'travelRoute' => $report1,
                'beersCollection' => $report2
            ]);

        }

        return $this->render('brewery/finder.html.twig', [
            'controller_name' => 'BreweryController',
            'form' => $form->createView(),
        ]);
    }

    private function getNavigation(GeoCode $home, $fuelDistance, DataProvider $provider)
    {
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
}
