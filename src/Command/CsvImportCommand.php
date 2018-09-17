<?php

namespace App\Command;

use App\Entity\Beer;
use App\Entity\Brewery;
use App\Entity\Category;
use App\Entity\GeoCode;
use App\Entity\Style;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CsvImportCommand extends Command
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setName('csv:import')
            ->setDescription('Imports a csv file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $files = array('breweries.csv', 'categories.csv', 'styles.csv', 'geocodes.csv', 'beers.csv');

        $io = new SymfonyStyle($input, $output);
        $io->title('Attempting to import the feed...');

        for ($i = 0; $i < count($files); $i++) {
            $reader = Reader::createFromPath('%kernel.root_dir%/../ZZZdumps/' . $files[$i]);
            $reader->setHeaderOffset(0);

            $io->text('Parsing ' . $files[$i] . ' ...');

            $results = $reader->getRecords();
            $io->progressStart(iterator_count($results));

            switch ($files[$i]) {
                case 'breweries.csv':
                    $this->persistBreweries($results, $io);
                    break;
                case 'categories.csv':
                    $this->persistCategories($results, $io);
                    break;
                case 'styles.csv':
                    $this->persistStyles($results, $io);
                    break;
                case 'geocodes.csv':
                    $this->persistGeoCodes($results, $io);
                    break;
                case 'beers.csv':
                    $this->persistBeers($results, $io);
                    break;
                default:
                    break;
            }

            $this->entityManager->flush();

            $io->progressFinish();
        }

        $io->success('Successfully done');
    }

    //todo: apsaugoti nuo klaidų

    private function persistBreweries($data, SymfonyStyle $io)
    {
        foreach ($data as $row) {
            $code = is_numeric($row['code']) ? $row['code'] : null;
            $user = is_numeric($row['add_user']) ? $row['add_user'] : null;

            $brewery = (new Brewery())
                ->setName($row['name'])
                ->setAddress1($row['address1'])
                ->setAddress2($row['address2'])
                ->setCity($row['city'])
                ->setState($row['state'])
                ->setCode($code)
                ->setCountry($row['country'])
                ->setPhone($row['phone'])
                ->setWebsite($row['website'])
                ->setFilepath($row['filepath'])
                ->setDescript($row['descript'])
                ->setAddUser($user)
                ->setLastMod(new \DateTime($row['last_mod']));

            $this->entityManager->persist($brewery);
            $io->progressAdvance();
        }
    }

    private function persistCategories($data, SymfonyStyle $io)
    {
        foreach ($data as $row) {
            $category = (new Category())
                ->setCatName($row['cat_name'])
                ->setLastMod(new \DateTime($row['last_mod']));

            $this->entityManager->persist($category);
            $io->progressAdvance();
        }
    }

    private function persistStyles($data, SymfonyStyle $io)
    {
        foreach ($data as $row) {

            $category = $this->entityManager->getRepository(Category::class)
                ->find($row['cat_id']);

            $style = (new Style())
                ->setCat($category)
                ->setStyleName($row['style_name'])
                ->setLastMod(new \DateTime($row['last_mod']));

            $this->entityManager->persist($style);
            $io->progressAdvance();
        }
    }

    private function persistGeoCodes($data, SymfonyStyle $io)
    {
        foreach ($data as $row) {

            $brewery = $this->entityManager->getRepository(Brewery::class)
                ->find($row['brewery_id']);

            $geoCode = (new GeoCode())
                ->setBrewery($brewery)
                ->setLatitude($row['latitude'])
                ->setLongitude($row['longitude'])
                ->setAccuracy($row['accuracy']);

            $this->entityManager->persist($geoCode);
            $io->progressAdvance();
        }
    }

    private function persistBeers($data, SymfonyStyle $io)
    {
        foreach ($data as $row) {

            $brewery = $this->entityManager->getRepository(Brewery::class)
                ->find($row['brewery_id']);

            $category = $this->entityManager->getRepository(Category::class)
                ->find($row['cat_id']);

            $style = $this->entityManager->getRepository(Style::class)
                ->find($row['style_id']);

            $user = is_numeric($row['add_user']) ? $row['add_user'] : null;

            $beer = (new Beer())
                ->setBrewery($brewery)
                ->setName($row['name'])
                ->setCat($category)
                ->setStyle($style)
                ->setAbv($row['abv'])
                ->setIbu($row['ibu'])
                ->setSrm($row['srm'])
                ->setUpc($row['upc'])
                ->setFilepath($row['filepath'])
                ->setDescript($row['descript'])
                ->setAddUser($user)
                ->setLastMod(new \DateTime($row['last_mod']));

            $this->entityManager->persist($beer);
            $io->progressAdvance();
        }
    }
}