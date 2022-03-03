<?php

namespace App\Repository;

use App\Entity\Sorties;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sorties|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sorties|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sorties[]    findAll()
 * @method Sorties[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortiesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sorties::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Sorties $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Sorties $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
   
    public function findSortie($id)
    {
        $em =  $this->getEntityManager();
        $dql =  "SELECT sorties.id,
                        sorties.nom, 
                        sorties.date_debut, 
                        sorties.duree, 
                        sorties.date_cloture_inscription, 
                        sorties.nb_inscriptions_max, 
                        sorties.description_infos, 
                        sorties.url_photo, 
                        participants.nom as organisateur, 
                        lieux.nom as lieu,
                        lieux.rue as rue,
                        lieux.latitude as latitude,
                        lieux.longitude as longitude,
                        villes.nom as ville,
                        villes.cp as cp, 
                        etats.libelle as etat, 
                        sites.nom as site 
                FROM App\Entity\Sorties sorties
                INNER JOIN App\Entity\Participants participants WITH sorties.id_organisateur = participants.id 
                INNER JOIN App\Entity\Lieux lieux WITH sorties.id_lieu = lieux.id 
                INNER JOIN App\Entity\Etats etats WITH sorties.id_etat = etats.id 
                INNER JOIN App\Entity\Sites sites WITH sorties.id_site = sites.id
                INNER JOIN App\Entity\Villes villes WITH lieux.id_ville = villes.id AND sorties.id = :id";
        $stmt = $em->createQuery($dql);
        $stmt->setParameter(':id', $id);
        return $stmt->getResult();
    }

    // /**
    //  * @return Sorties[] Returns an array of Sorties objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sorties
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
