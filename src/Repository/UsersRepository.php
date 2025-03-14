<?php

namespace App\Repository;

use App\Entity\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @extends ServiceEntityRepository<Users>
 */
class UsersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry,private readonly Security $security)
    {
        parent::__construct($registry, Users::class);
    }

       /**
        * @return Users[] Returns an array of Users objects
        */
       public function countUsers(): int
       {
           return $this->createQueryBuilder('u')
               ->select('count(u.id)')
               ->getQuery()
               ->getSingleScalarResult()
           ;
       }

       public function findCurrentUser(): ?Users
       {
          /** @var Users $user */
        $user = $this->security->getUser();

           return $this->createQueryBuilder('u')
               ->andWhere('u.id = :val')
               ->setParameter('val', $user->getId())
               ->getQuery()
               ->getOneOrNullResult()
           ;
       }
}
