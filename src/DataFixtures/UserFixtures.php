<?php
namespace App\DataFixtures;
use Faker\Factory;
use App\Entity\Book;
use App\Entity\User;
use App\Entity\Genre;
use App\Entity\Image;
use App\Entity\Author;
use App\Entity\Address;
use App\Entity\Command;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Tests\Fixtures\Validation\Category;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    private $entityManager;
    public function __construct(UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
    }
    public function load(ObjectManager $objectManager)
    {

        $faker = \Faker\Factory::create('fr_FR');
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
        // ===== Categories ===============================================
//        $genre1 = new Genre();
//        $genre1->setName('Histoire');
//        $this->entityManager->persist($genre1);
        //-----------------------------------
//        $genre2 = new Genre();
//        $genre2->setName('Politique');
//        $this->entityManager->persist($genre2);
        // ----------------------------------
//        $genre3 = new Genre();
//        $genre3->setName('Humour');
//        $this->entityManager->persist($genre3);

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
        ->addGenre($this->getReference("genreReference_".random_int(0, count(BookFixtures::GENRES) - 1 )));
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
        ->addGenre($this->getReference("genreReference_".random_int(0, count(BookFixtures::GENRES) - 1 )));
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
        ->addGenre($this->getReference("genreReference_".random_int(0, count(BookFixtures::GENRES) - 1 )));
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
        // ===== Commands ======================================================
        $command1 = new Command();

        $command1->setDate(new \DateTimeImmutable())
        ->setNumber("8657185758")
        ->setQuantity(2)
        ->setTotalcost(44.0)
        ->setState("en cours")
        ->addBook($book2)
        ->addBook($book1);
        $this->entityManager->persist($command1);
        // ------------------------------------------
        $command2 = new Command();

        // $entityManager->flush();

        $command2->setDate(new \DateTimeImmutable())
        ->setNumber("8917186412")
        ->setQuantity(3)
        ->setTotalcost(22.0)
        ->setState("expédié")
        ->addBook($book1)
        ->addBook($book2)
        ->addBook($book3);

        $this->entityManager->persist($command2);
        // ------------------------------------------
        $command3 = new Command();
        $command3->setDate(new \DateTimeImmutable())
        ->setNumber("8917186412")
        ->setQuantity(3)
        ->setTotalcost(22.0)
        ->setState("expédié")
        ->addBook($book1)
        ->addBook($book2)
        ->addBook($book3);

        $this->entityManager->persist($command3);
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
        ->setLastname("Pierre")
        ->addCommandFacturation($command3)
        ->addCommandLivraison($command3);

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
        ->setLastname("Pierre")
        ->addCommandFacturation($command1)
        ->addCommandLivraison($command2);
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
        ->setLastname("Dujardin")
        ->addCommandFacturation($command2)
        ->addCommandLivraison($command1);

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
         ->setBirth(new \DateTime())
         ->addCommand($command1)
         ->addCommand($command2)
         ->addCommand($command3)
         ->addAddress($address1)
         ->addAddress($address2)
         ->addAddress($address3);

        $this->entityManager->persist($user);

        $this->entityManager->flush();
    }
}