<?php
namespace App\DataFixtures;

use DateTime;
use Faker\Factory;
use App\Entity\Book;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Author;
use App\Entity\Address;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserFixtures extends Fixture
{

    private UserPasswordEncoderInterface $passwordEncoder;
    private EntityManagerInterface $entityManager;


    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $entityManager
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
    }


    public function load(ObjectManager $objectManager)
    {

        $faker = Factory::create('fr_FR');
        // ==== Images ==========================================================
        $image1 = new Image();

        $image1->setUrl("/build/images/livre1.jpg");
        $this->entityManager->persist($image1);
        // -------------------------------------
        $image2 = new Image();

        $image2->setUrl("/build/images/livre2.jpg");
        $this->entityManager->persist($image2);
        // ------------------------------------
        $image3 = new Image();

        $image3->setUrl("/build/images/livre3.jpg");
        $this->entityManager->persist($image3);

        // ===== Books =====================================================
        $book1 = new Book();

        $book1->setDescription("Un beau livre, bien propre, en papier. Il raconte un belle histoire, passionante, dense et bien documentée.")
            ->setPrice(24)
            ->setIsbn("6486158165")
            ->setStock(5)
            ->setTitle("Le titre du livre")
            ->setYear(2002)
            ->setLength(15)
            ->setHeight(40)
            ->setWidth(10)
            ->setNew($faker->numberBetween($min = 0, $max = 1))
            ->addImage($image1)
            ->addGenre($this->getReference("genreReference_".random_int(0, count(BookFixtures::GENRES) - 1)));
        $this->entityManager->persist($book1);
        // ---------------------------------------------
        $book2 = new Book();

        $book2->setDescription("Le meilleur livre du monde à lire absolument. Ce livre changera votre vision du monde et votre compréhension de vous même.")
            ->setPrice(18)
            ->setIsbn("87521463258")
            ->setStock(8)
            ->setTitle("Le meilleur livre du monde")
            ->setYear(2007)
            ->setLength(30)
            ->setHeight(35)
            ->setWidth(21)
            ->setNew($faker->numberBetween($min = 0, $max = 1))
            ->addImage($image2)
            ->addGenre($this->getReference("genreReference_".random_int(0, count(BookFixtures::GENRES) - 1)));
        $this->entityManager->persist($book2);
        // ----------------------------------------------
        $book3 = new Book();

        $book3->setDescription("C'est l'histoire d'un pingouin qui vie au pole nord et qui aime nager et manger du poisson")
            ->setPrice(15)
            ->setIsbn("44215889753")
            ->setStock(10)
            ->setTitle("L'histoire du pingouin")
            ->setYear(1978)
            ->setLength(18)
            ->setWidth(12)
            ->setHeight(50)
            ->setNew($faker->numberBetween($min = 0, $max = 1))
            ->addImage($image3)
            ->addGenre($this->getReference("genreReference_".random_int(0, count(BookFixtures::GENRES) - 1)));
        $this->entityManager->persist($book3);
        // ===== Authors ========================================================
        $author1 = new Author();

        $author1->setFirstname("Alain")
            ->setLastname("Jean")
            ->addBook($book1);
        $this->entityManager->persist($author1);
        // ---------------------------------
        $author2 = new Author();

        $author2->setFirstname("Bernard")
            ->setLastname("Pinot")
            ->addBook($book2)
            ->addBook($book3);
        $this->entityManager->persist($author2);
        // ===== Adresses ======================================================
        $address1 = new Address();
        $address1->setNumber("2")
            ->setType("bis")
            ->setStreet("rue du pont")
            ->setCity("vesoul")
            ->setCp("70000")
            ->setCountry("France")
            ->setAdditional("appartement 12")
            ->setTitle("Maison")
            ->setFirstname("Jean")
            ->setLastname("Pierre");

        $this->entityManager->persist($address1);
        // -----------------------------------------
        $address2 = new Address();

        $address2->setNumber("7")
            ->setStreet("rue du chien")
            ->setCity("besançon")
            ->setCp("25000")
            ->setCountry("France")
            ->setAdditional("cave 5")
            ->setTitle("Bureau")
            ->setFirstname("Jean")
            ->setLastname("Pierre");

        $this->entityManager->persist($address2);
        // ----------------------------------------
        $address3 = new Address();
        $address3->setNumber("3")
            ->setType("bis")
            ->setStreet("rue du chat")
            ->setCity("dijon")
            ->setCp("39000")
            ->setCountry("France")
            ->setTitle("Voisin")
            ->setFirstname("Thomas")
            ->setLastname("Dujardin");

        $this->entityManager->persist($address3);

         // ===== User ======================================================
         $user = new User();

         $hash = $this->passwordEncoder->encodePassword($user, "online@2017");
         $user->setFirstname("Lucas")
             ->setLastname("Robin")
             ->setTel("0649357680")
             ->setUsername("lucas.rob1@live.fr")
             ->setRoles(["ROLE_USER"])
             ->setPassword($hash)
             ->setGender('homme')
             ->setBirth(new DateTime())
             ->addAddress($address1)
             ->addAddress($address2)
             ->addAddress($address3);

        $this->entityManager->persist($user);

        $this->entityManager->flush();


        // ===== Admin ======================================================

        $adminAddress = new Address();

        $adminAddress->setNumber("")
            ->setStreet("Boite Postale 1038")
            ->setCp("70001")
            ->setCity("Vesoul Cedex")
            ->setCountry("France")
            ->setTitle("VesoulEdition")
            ->setFirstname("Christian")
            ->setLastname("Petit");

        $this->entityManager->persist($adminAddress);
        $this->entityManager->flush();


        $admin = new User();
        $hash = $this->passwordEncoder->encodePassword($admin, "online@2017");

        $admin->setUsername("vesouledition@sfr.fr")
            ->setFirstname("Christian")
            ->setLastname("Petit")
            ->setTel("0699658600")
            ->setRoles(["ROLE_ADMIN"])
            ->setPassword($hash)
            ->setGender('homme')
            ->setBirth(new DateTime())
            ->addAddress($adminAddress);

        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        // ======================================================

    }
}
