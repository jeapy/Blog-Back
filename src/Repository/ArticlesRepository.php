<?php

namespace App\Repository;

use App\Entity\Articles;
use App\Entity\Enum\ArticleStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Integer;

/**
 * @extends ServiceEntityRepository<Articles>
 */
class ArticlesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Articles::class);
    }

       /**
        * @return Articles[] Returns an array of Articles objects
        */
       public function countPublishedArticle(): int
       {
           return $this->createQueryBuilder('a')
                ->select('count(a.id)')
               ->andWhere('a.status = :val')
               ->setParameter('val', ArticleStatus::PUBLIER)
               ->getQuery()
               ->getSingleScalarResult()
           ;
       }

        /**
        * @return Articles[] Returns an array of Articles objects
        */
        public function countArchivedArticle(): int
        {
            return $this->createQueryBuilder('a')
                 ->select('count(a.id)')
                ->andWhere('a.status = :val')
                ->setParameter('val', ArticleStatus::ARCHIVER)
                ->getQuery()
                ->getSingleScalarResult()
            ;
        }

    //    public function findOneBySomeField($value): ?Articles
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
