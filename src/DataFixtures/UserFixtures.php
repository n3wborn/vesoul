<?php
namespace App\DataFixtures;
use Faker\Factory;
use App\Entity\Book;
use App\Entity\User;
use App\Entity\Genra;
use App\Entity\Image;
use App\Entity\Author;
use App\Entity\Address;
use App\Entity\Command;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Tests\Fixtures\Validation\Category;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    public function __construct(UserPasswordEncoderInterface $passwordEncoder) 
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    public function load(ObjectManager $objectManager)
    {   
        
        $faker = \Faker\Factory::create('fr_FR');
        // ==== Images ==========================================================
        $image1 = new Image();
    
        $image1->setUrl("/build/images/livre1.jpg");
        $objectManager->persist($image1);
        // -------------------------------------
        $image2 = new Image();
        
        $image2->setUrl("/build/images/livre2.jpg");
        $objectManager->persist($image2);
        // ------------------------------------
        $image3 = new Image();
        
        $image3->setUrl("/build/images/livre3.jpg");
        $objectManager->persist($image3);
        // ===== Categories ===============================================
        $genra1 = new Genra();
        $genra1->setName('Histoire');
        $objectManager->persist($genra1);
        //-----------------------------------
        $genra2 = new Genra();
        $genra2->setName('Politique');
        $objectManager->persist($genra2);
        // ----------------------------------
        $genra3 = new Genra();
        $genra3->setName('Humour');
        $objectManager->persist($genra3);
         
        // ===== Books =====================================================
        $book1 = new Book();
        
        $book1->setDescription("Un beau livre, bien propre, en papier. Il raconte un belle histoire, passionante, dense et bien documentée.")
        ->setPrice(24)
        ->setIsbn("6486158165")
        ->setStock(5)
        ->setTitle("Le titre du livre")
        ->setYear(2002)
        ->setLength(15)
        ->setWidth(10)
        ->setNew($faker->numberBetween($min = 0, $max = 1))
        ->addImage($image1)
        ->addGenra($genra1);
        $objectManager->persist($book1);
        // ---------------------------------------------
        $book2 = new Book();
        
        $book2->setDescription("Le meilleur livre du monde à lire absolument. Ce livre changera votre vision du monde et votre compréhension de vous même.")
        ->setPrice(18)
        ->setIsbn("87521463258")
        ->setStock(8)
        ->setTitle("Le meilleur livre du monde")
        ->setYear(2007)
        ->setLength(30)
        ->setWidth(21)
        ->setNew($faker->numberBetween($min = 0, $max = 1))
        ->addImage($image2)
        ->addGenra($genra2);
        $objectManager->persist($book2);
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
        ->setNew($faker->numberBetween($min = 0, $max = 1))
        ->addImage($image3)
        ->addGenra($genra3);
        $objectManager->persist($book3);
        // ===== Authors ========================================================
        $author1 = new Author();
        
        $author1->setFirstname("Alain")
        ->setLastname("Jean")
        ->addBook($book1);
        $objectManager->persist($author1);
        // ---------------------------------
        $author2 = new Author();
        
        $author2->setFirstname("Bernard")
        ->setLastname("Pinot")
        ->addBook($book2)
        ->addBook($book3);
        $objectManager->persist($author2);
        // ===== Commands ======================================================
        $command1 = new Command();
        
        $command1->setDate(new \DateTimeImmutable())
        ->setNumber("8657185758")
        ->setQuantity(2)
        ->setTotalcost(44.0)
        ->setState("en cours")
        ->addBook($book2)
        ->addBook($book1);
        $objectManager->persist($command1);
        // ------------------------------------------
        $command2 = new Command();
        
        // $objectManager->flush();
        
        $command2->setDate(new \DateTimeImmutable())
        ->setNumber("8917186412")
        ->setQuantity(3)
        ->setTotalcost(22.0)
        ->setState("expédié")
        ->addBook($book1)
        ->addBook($book2)
        ->addBook($book3);
        
        $objectManager->persist($command2);
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
        
        $objectManager->persist($command3);
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
        
        $objectManager->persist($address1);
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
        $objectManager->persist($address2);
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
        
        $objectManager->persist($address3);
        
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
         ->setNewsletter(true)
         ->setBirth(new \DateTime())
         ->addCommand($command1)
         ->addCommand($command2)
         ->addCommand($command3)
         ->addAddress($address1)
         ->addAddress($address2)
         ->addAddress($address3);
 
         $objectManager->persist($user);
 
         $objectManager->flush();
    }
}