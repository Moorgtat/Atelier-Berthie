<?php

namespace App\Repository;

use App\Service\Search;
use App\Entity\Produit;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    /**
     * Requete qui me permets de recuperer les produits e fonction de la recherche de l'utilisateur
     *
     * @param Search $search
     * @return Produit[]
     */
    public function findWithSearch(Search $search) 
    {
        $query = $this
            ->createQueryBuilder('p')
            ->select('c', 'p')
            ->join('p.categorie', 'c');
        
            if (!empty($search->categories)) {
                $query = $query
                ->andWhere('c.id IN (:categories)')
                ->setParameter('categories', $search->categories);
            }

            if (!empty($search->string)) {
                $query = $query
                ->andWhere('p.titre LIKE :string')
                ->setParameter('string', "%{$search->string}%");
            }

            return $query->getQuery()->getResult();
    }

    // /**
    //  * @return Produit[] Returns an array of Produit objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Produit
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
