<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory;
use App\Entity\Book;
use App\Entity\Genra;
use App\Entity\Image;
use App\Entity\Author;
use Doctrine\Persistence\ObjectManager;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service_locator;

class BookFixtures extends Fixture
{
    const GENRES = [
        "Policier",
        "SF",
        "Humour",
        "Bande déssinée",
        "Histoire",
        "Horreur",
        "Guide pratique",
        "Théâtre",
        "Scolaire",
        "Voyages"
    ];



    public function load(ObjectManager $manager)
    {
        $i = 0;
        foreach( self::GENRES as $value ){
            $genre = new Genra();
            $genre->setName($value);
            $manager->persist($genre);

            $this->addReference( "genreReference_".$i , $genre);
            $i++;
        }

        $manager->flush();


        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 100; $i++) {
            $image = new Image();
            $book = new Book();
            $author = new Author();

            $image->setUrl('https://picsum.photos/640/480');
            $manager->persist($image);

            $book->setDescription($faker->sentence($nbWords = 6, $variableNbWords = true))
            ->setPrice($faker->numberBetween($min = 10, $max = 40))
            ->setIsbn(strval($faker->isbn10))
            ->setStock($faker->numberBetween($min = 1, $max = 30))
            ->setTitle($faker->sentence($nbWords = 3, $variableNbWords = true))
            ->setYear($faker->numberBetween($min = 1940, $max = 2019))
            ->setLength($faker->randomElement($array = array (15,25,40)))
            ->setWidth($faker->randomElement($array = array (15,25,40)))
            ->setHeight($faker->randomElement($array = array (15,25,40)))
            ->setNew($faker->numberBetween($min = 0, $max = 1))
            ->addImage($image)
            ->addGenra($this->getReference("genreReference_".random_int(0, count(self::GENRES ) - 1 )));

            $manager->persist($book);

            $author->setFirstname($faker->firstNameMale)
            ->setLastname($faker->lastName)
            ->addBook($book);

            $manager->persist($author);
            $manager->flush();
        }
    }
}
