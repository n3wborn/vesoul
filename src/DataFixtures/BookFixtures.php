<?php

namespace App\DataFixtures;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\Book;
use App\Entity\User;
use App\Entity\Genra;
use App\Entity\Image;
use App\Entity\Author;
use App\Entity\Address;
use App\Entity\Command;

class BookFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');

            for ($i = 0; $i < 100; $i++) {
                
            $genra = new Genra();
            $image = new Image(); 
            $book = new Book();
            $author = new Author();

            //$image->setUrl($faker->imageUrl($width = 640, $height = 480));
            $image->setUrl('https://picsum.photos/640/480');
            $manager->persist($image);

            $genra->setName($faker->randomElement($array = array ('Science-Fiction','Roman','Histoire','Politique','Humour')));
            $manager->persist($genra);

            $book->setDescription($faker->sentence($nbWords = 6, $variableNbWords = true))
            ->setPrice($faker->numberBetween($min = 10, $max = 40))
            ->setIsbn(strval($faker->isbn10))
            ->setStock($faker->numberBetween($min = 1, $max = 30))
            ->setTitle($faker->sentence($nbWords = 3, $variableNbWords = true))
            ->setYear($faker->numberBetween($min = 1940, $max = 2019))
            ->setLength($faker->randomElement($array = array (15,25,40)))
            ->setWidth($faker->randomElement($array = array (15,25,40)))
            ->setNew($faker->numberBetween($min = 0, $max = 1))
            ->addImage($image)
            ->addGenra($genra);

            $manager->persist($book);

            $author->setFirstname($faker->firstNameMale)
            ->setLastname($faker->lastName)
            ->addBook($book);

            $manager->persist($author);
            $manager->flush();
        }
    }
}
