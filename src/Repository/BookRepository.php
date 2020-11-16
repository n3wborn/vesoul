<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Mapping\OrderBy;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{   

    public const LIMIT = 9;


    public function __construct(\Doctrine\Common\Persistence\ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    
    public function findTitle($title){

        return $this->createQueryBuilder('b')
                    ->select('b.id, b.title')
                    ->andWhere('b.title like :title')
                    ->setParameter(':title', '%'.$title.'%')
                    ->orderBy('b.id', 'ASC')
                    ->setMaxResults(10)
                    ->getQuery()
                    ->getResult();
    }

    public function maxAndMinYear(){
        return $this->createQueryBuilder('b')
                    ->select('max(b.year) as maxyear, min(b.year) as minyear')
                    ->getQuery()
                    ->getScalarResult();
    }

    public function countBooks($new, $genre, $author, $yearmin, $yearmax, $title){
        
        $queryParamaters = [];

        $query =  $this->createQueryBuilder('b')
                        ->select('count(b.id) as count')
                        ->join('b.genres', 'g')
                        ->join('b.author', 'a');

        if( $new === "true" ){
            $query = $query->andWhere('b.new = :new');
            $queryParamaters[':new'] = $new;
        }

        if( count($genre) > 0 ){

            $queryGenre = "";

            for( $i = 0; $i < count($genre); $i++){
                $queryGenre .= ($i === 0 ) ? 'g.name = :genre'.$i : ' OR g.name = :genre'.$i ;
                $queryParamaters['genre'.$i] = $genre[$i];
            }
            
            $query = $query->andWhere($queryGenre);
        }

        if( count($author) > 0 ){

            $queryAuthor = "";

            for( $i = 0; $i < count($author); $i++){
                $authorFirstAndLastName = explode('-', $author[$i]);
                if( count($authorFirstAndLastName) === 2 ){
                    $queryAuthor .= ($i === 0 ) ? ' ( a.firstname = :authorfirstname'.$i.' and a.lastname = :authorlastname'.$i.' ) ' : ' OR ( a.firstname = :authorfirstname'.$i.' and a.lastname = :authorlastname'.$i.' ) ' ;
                    $queryParamaters[':authorfirstname'.$i] = $authorFirstAndLastName[0];
                    $queryParamaters[':authorlastname'.$i] = $authorFirstAndLastName[1];
                }
            }
            
            $query = $query->andWhere($queryAuthor);
            
        }


        if( strlen( $title ) > 0 ){
            $query = $query->andWhere('b.title like :title');
            $queryParamaters[':title'] = '%'.$title.'%';
        }

       

        $query = $query->andWhere('b.year >= :yearmin and b.year <= :yearmax');
        $queryParamaters[':yearmin'] = $yearmin;
        $queryParamaters[':yearmax'] = $yearmax;

        if(count($queryParamaters) > 0 ){
            $query = $query->setParameters($queryParamaters);
        }
        
        $query = $query->getQuery()
        ->getSingleScalarResult();

        return $query;
        
    }

    public function findAllBooksByAscName(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = ' 
            SELECT book.id, book.price, book.title, book.stock, book.year, author.id, author.firstname, author.lastname, image.url, genre.name AS genre
            FROM book
            INNER JOIN  author ON book.author_id = author.id
            INNER JOIN image ON book.id = image.book_id
            INNER jOIN book_genre ON book.id = book_genre.book_id
            INNER JOIN genre ON book_genre.genre_id = genre.id
            ORDER BY book.title
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
    
        // returns an array of book
        return $stmt->fetchAll();
    }

    public function findAllBooksByDescName(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = ' 
            SELECT book.id, book.price, book.title, book.stock, book.year, author.firstname, author.lastname, image.url, genre.name AS genre
            FROM book
            INNER JOIN  author ON book.author_id = author.id
            INNER JOIN image ON book.id = image.book_id
            INNER jOIN book_genre ON book.id = book_genre.book_id
            INNER JOIN genre ON book_genre.genre_id = genre.id
            ORDER BY book.title DESC
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
    
        // returns an array of book
        return $stmt->fetchAll();
    }

    public function findAllBooksByAscYear(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = ' 
            SELECT book.id, book.price, book.title, book.stock, book.year, author.firstname, author.lastname, image.url, genre.name AS genre
            FROM book
            INNER JOIN  author ON book.author_id = author.id
            INNER JOIN image ON book.id = image.book_id
            INNER jOIN book_genre ON book.id = book_genre.book_id
            INNER JOIN genre ON book_genre.genre_id = genre.id
            ORDER BY book.year
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
    
        // returns an array of book
        return $stmt->fetchAll();
    }

    public function findAllBooksByDescYear(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = ' 
            SELECT book.id, book.price, book.title, book.stock, book.year, author.firstname, author.lastname, image.url, genre.name AS genre
            FROM book
            INNER JOIN  author ON book.author_id = author.id
            INNER JOIN image ON book.id = image.book_id
            INNER jOIN book_genre ON book.id = book_genre.book_id
            INNER JOIN genre ON book_genre.genre_id = genre.id
            ORDER BY book.year DESC
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
    
        // returns an array of book
        return $stmt->fetchAll();
    }

    public function findBook($id): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = ' 
            SELECT book.description, book.price, book.isbn, book.title, book.stock, book.year, book.length, book.width, author.id, author.firstname, author.lastname, genre.name AS genre, image.url
            FROM book
            INNER JOIN  author ON book.author_id = author.id
            INNER jOIN book_genre ON book.id = book_genre.book_id
            INNER JOIN genre ON book_genre.genre_id = genre.id
            INNER JOIN image ON book.id = image.book_id
            WHERE book.id = :book_id
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['book_id' => $id]);
    
        // returns an array of book
        return $stmt->fetchAll();
    }

    // /**
    //  * @return Book[] Returns an array of Book objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Book
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findPageOfListBook($offset, $orderBy, $new, $genre, $author, $yearmin, $yearmax, $title) {

        $fieldOrderBy = 'title';
        $howOrderBy = 'ASC';
        $queryParamaters = [];

        switch($orderBy){

            case 'ascName' : 
                $fieldOrderBy = 'title';
                $howOrderBy = 'ASC';
                break;
            case 'descName' : 
                $fieldOrderBy = 'title';
                $howOrderBy = 'DESC';
                break;
            case 'ascYear' : 
                $fieldOrderBy = 'year';
                $howOrderBy = 'ASC';
                break;
            case 'descYear' : 
                $fieldOrderBy = 'year';
                $howOrderBy = 'DESC';
                break;
        }
        
        $query =  $this->createQueryBuilder('b')
                        ->select('b','a','i')
                        ->join('b.author', 'a')
                        ->join('b.images', 'i')
                        ->join('b.genres', 'g');

        if( $new === "true" ){
            $query = $query->andWhere('b.new = :new');
            $queryParamaters[':new'] = $new;
        }

        if( count($genre) > 0 ){

            $queryGenre = "";

            for( $i = 0; $i < count($genre); $i++){
                $queryGenre .= ($i === 0 ) ? 'g.name = :genre'.$i : ' OR g.name = :genre'.$i ;
                $queryParamaters[':genre'.$i] = $genre[$i];
            }
            
            $query = $query->andWhere($queryGenre);
            
        }

        if( count($author) > 0 ){

            $queryAuthor = "";
            

            for( $i = 0; $i < count($author); $i++){
                $authorFirstAndLastName = explode('-', $author[$i]);
                if( count($authorFirstAndLastName) === 2 ){
                    $queryAuthor .= ($i === 0 ) ? ' ( a.firstname = :authorfirstname'.$i.' and a.lastname = :authorlastname'.$i.' ) ' : ' OR ( a.firstname = :authorfirstname'.$i.' and a.lastname = :authorlastname'.$i.' ) ' ;
                    $queryParamaters[':authorfirstname'.$i] = $authorFirstAndLastName[0];
                    $queryParamaters[':authorlastname'.$i] = $authorFirstAndLastName[1];
                }
            }
            
            $query = $query->andWhere($queryAuthor);
            
        }


        if( strlen( $title ) > 0 ){
            $query = $query->andWhere('b.title like :title');
            $queryParamaters[':title'] = '%'.$title.'%';
        }

        $query = $query->andWhere('b.year >= :yearmin and b.year <= :yearmax');
        $queryParamaters[':yearmin'] = $yearmin;
        $queryParamaters[':yearmax'] = $yearmax;


        if(count($queryParamaters) > 0 ){
            $query = $query->setParameters($queryParamaters);
        }
        
        $query = $query->orderBy('b.'.$fieldOrderBy, $howOrderBy)        
                        ->setFirstResult( $offset )
                        ->setMaxResults( self::LIMIT )
                        ->getQuery()
                        ->getArrayResult();
        
        

        return $query;
    }

    public function searchByTitle(  $title ) {

        $fieldOrderBy = 'title';
        $howOrderBy = 'ASC';
        
        $query =  $this->createQueryBuilder('b')
                        ->select('b','a','i')
                        ->join('b.author', 'a')
                        ->join('b.images', 'i')
                        ->join('b.genres', 'g')
                        ->andWhere(' b.title like :title ')
                        ->setParameter(':title', '%'.$title.'%')
                        ->orderBy('b.'.$fieldOrderBy, $howOrderBy)
                        ->getQuery()
                        ->getArrayResult();

        return $query;   
    }
}
