<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory;
use App\Entity\Book;
use App\Entity\Genre;
use App\Entity\Image;
use App\Entity\Author;
use Doctrine\Persistence\ObjectManager;

class BookFixtures extends Fixture
{
    const GENRES = [
        "Policier",
        "SF",
        "Humour",
        "Bande dessinée",
        "Histoire",
        "Horreur",
        "Guide pratique",
        "Théâtre",
        "Scolaire",
        "Voyages"
    ];



    public function load(ObjectManager $manager)
    {

        // create Genres references
        for ($i=0; $i < count(self::GENRES); $i++) {
            $genre = new Genre();
            $genre->setName(self::GENRES[$i]);
            $manager->persist($genre);

            $this->addReference( "genreReference_".$i , $genre);
        }


        // start fixture creation
        $faker = Factory::create('fr_FR');


        // create 10 product
        for ($i = 0; $i < 10; $i++) {


            $image = new Image();
            $book = new Book();
            $author = new Author();


            $image->setUrl('https://picsum.photos/640/480');


            // create books
            $book->setDescription($faker->sentence(6, true))
                ->setPrice($faker->numberBetween(5, 100))
                ->setIsbn(strval($faker->isbn10))
                ->setStock($faker->numberBetween(1, 30))
                ->setTitle($faker->sentence(3, true))
                ->setYear($faker->numberBetween(1940, 2020))
                ->setLength($faker->numberBetween(10, 400))
                ->setWidth($faker->numberBetween(10, 50))
                ->setHeight($faker->numberBetween(10, 50))
                ->setNew($faker->numberBetween(0, 1))
                ->addImage($image)
                ->addGenre($this->getReference("genreReference_".random_int(0, count(self::GENRES ) - 1 )));

            // save it
            $manager->persist($book);

            // give books an author
            $author->setFirstname($faker->firstNameMale)
                ->setLastname($faker->lastName)
                ->addBook($book);

            // save them
            $manager->persist($author);

            // keep in db
            $manager->flush();
        }
    }
}
