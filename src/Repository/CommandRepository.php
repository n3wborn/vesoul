<?php

namespace App\Repository;

use App\Entity\Command;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Command|null find($id, $lockMode = null, $lockVersion = null)
 * @method Command|null findOneBy(array $criteria, array $orderBy = null)
 * @method Command[]    findAll()
 * @method Command[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Command::class);
    }

    // /**
    //  * @return Order[] Returns an array of Order objects
    //  */
    public function findCommandByUserId($value)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT command.livraison_id, command.facturation_id, command.date, command.number, command.quantity, command.totalcost, command.state
            FROM command
            WHERE command.user_id = :value
            ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['value' => $value]);
    
        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
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
