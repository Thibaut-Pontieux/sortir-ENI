<?php

namespace App\Repository;

use App\Entity\Sorties;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\BooleanType;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Config\Definition\BooleanNode;
use Symfony\Component\Validator\Constraints\Date;
use function Doctrine\ORM\QueryBuilder;

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

    // /**
    //  * @return Sorties[] Returns an array of Sorties objects with ID transformed into name
    //  */
   public function findAllSorties()
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
                        etats.libelle as etat, 
                        sites.nom as site 
                FROM App\Entity\Sorties sorties 
                INNER JOIN App\Entity\Participants participants WITH sorties.id_organisateur = participants.id 
                INNER JOIN App\Entity\Lieux lieux WITH sorties.id_lieu = lieux.id 
                INNER JOIN App\Entity\Etats etats WITH sorties.id_etat = etats.id 
                INNER JOIN App\Entity\Sites sites WITH sorties.id_site = sites.id";
        $stmt = $em->createQuery($dql);
        return $stmt->getResult();
    }

    // /**
    //  * @return Sorties[] Returns an array of Sorties objects filtered by what user ask
    //  */
    public function findFilteredSorties(
        Int $site,
        String $sortie
        //Date $dateDebut,
        //Date $dateFin,
        //BooleanType $organisateur,
        //BooleanNode $inscrit,
        //BooleanNode $nonInscrit,
        //BooleanNode $sortiesPassees
    ){
        $res = $this->createQueryBuilder('s');
        //$res->setParameter('dateDebut', '%'.$dateDebut.'%');
        //$res->setParameter('dateFin', '%'.$dateFin.'%');
        //$res->setParameter('organisateur', '%'.$organisateur.'%');
        //$res->setParameter('inscrit', '%'.$inscrit.'%');
        //$res->setParameter('nonInscrit', '%'.$nonInscrit.'%');
        //$res->setParameter('sortiePassees', '%'.$sortiesPassees.'%');

        if($site !== 0){
            $res->where($res->expr()->eq('s.id_site',':site'));
            $res->setParameter('site', $site);
        }

        if($sortie !== ""){
            $res->andwhere($res->expr()->like('s.nom',':sortie'));
            $res->setParameter('sortie', '%'.$sortie.'%');
        }
        /*if(dateDebut !== ""){

        }
        if(dateFin !== ""){

        }
        if(organisateur ==""){

        }
        if(inscrit == ""){

        }
        if(nonInscrit == ""){

        }
        if(sortiePassees ==""){

        }*/

        $res->getQuery()
            ->getResult();
        dump($res);





        return $res;


        /*
        if($site){
            $res->setParameter('site',$site);
            $res->where('s.id_site = :site');
        }
        if($sortie){
            $res->setParameter('sortie',$sortie);
            $res->where('s.id_site = :site');
            $res->expr()->like('sortie');
        }
        if($dateDebut){

        }
        if($dateFin){

        }
        if($organisateur){

        }
        if($inscrit){

        }
        if($nonInscrit){

        }
        if($sortiesPassees){

        }

        return $res;
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;
        */
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
