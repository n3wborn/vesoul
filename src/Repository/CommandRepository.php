<?php

namespace App\Repository;

use App\Entity\Command;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Command|null find($id, $lockMode = null, $lockVersion = null)
 * @method Command|null findOneBy(array $criteria, array $orderBy = null)
 * @method Command[]    findAll()
 * @method Command[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Command::class);
    }


    /**
     * @method findUserCommands
     * @return Command[] Return an array of Command objects
     */
    public function findUserCommands($user) : array
    {
        return $this->createQueryBuilder('c')
            ->Where('c.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult()
        ;
    }

    // public function findOneById($value)
    // {
    //     $conn = $this->getEntityManager()->getConnection();

    //     $sql = '
    //         SELECT command.date, command.number, command.quantity, command.totalcost, user.firstname, user.lastname,
    //         COUNT(book.isbn)
    //         FROM command
    //         INNER JOIN user ON user.id = command.user_id
    //         INNER JOIN command_book ON command_book.command_id = command.id
    //         INNER JOIN book ON book.id = command_book.book_id
    //         WHERE command.id = :value
    //         ';
    //     $stmt = $conn->prepare($sql);
    //     $stmt->execute(['value' => $value]);
    
    //     // returns an array of arrays (i.e. a raw data set)
    //     return $stmt->fetchAll();
    //     ;
    // }
}
