<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\BooleanType;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Config\Definition\BooleanNode;
use Symfony\Component\Validator\Constraints\Date;
use DateTime;
use function Doctrine\ORM\QueryBuilder;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Sortie $entity, bool $flush = true): void
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
    public function remove(Sortie $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findSortie($id)
    {
        $em =  $this->getEntityManager();
        $dql =  "SELECT sortie.id,
                        sortie.nom, 
                        sortie.dateDebut, 
                        sortie.duree, 
                        sortie.dateClotureInscription, 
                        sortie.nbInscriptionsMax, 
                        sortie.descriptionInfos, 
                        sortie.urlPhoto, 
                        participant.nom as organisateur, 
                        lieu.nom as lieu,
                        lieu.rue as rue,
                        lieu.latitude as latitude,
                        lieu.longitude as longitude,
                        ville.nom as ville,
                        ville.cp as cp, 
                        etat.libelle as etat, 
                        site.nom as site 
                FROM App\Entity\Sortie sortie
                INNER JOIN App\Entity\Participant participant WITH sortie.participant = participant.id 
                INNER JOIN App\Entity\Lieu lieu WITH sortie.lieu = lieu.id 
                INNER JOIN App\Entity\Etat etat WITH sortie.etat = etat.id 
                INNER JOIN App\Entity\Site site WITH sortie.site = site.id
                INNER JOIN App\Entity\Ville ville WITH lieu.ville = ville.id AND sortie.id = :id";
        $stmt = $em->createQuery($dql);
        $stmt->setParameter(':id', $id);
        return $stmt->getResult();
    }

    // /**
    //  * @return Sortie[] Returns an array of Sortie objects with ID transformed into name
    //  */
   public function findAllSorties()
    {
        /*$em =  $this->getEntityManager();
        $dql = "SELECT sorties.id,
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
                FROM App\Entity\Sortie sorties 
                INNER JOIN App\Entity\Participant participants WITH sorties.id_organisateur = participants.id 
                INNER JOIN App\Entity\Lieu lieux WITH sorties.id_lieu = lieux.id 
                INNER JOIN App\Entity\Etat etats WITH sorties.id_etat = etats.id 
                INNER JOIN App\Entity\Site sites WITH sorties.id_site = sites.id";
        $stmt = $em->createQuery($dql);
        return $stmt->getResult();*/

        $res = $this->createQueryBuilder('s')
            ->join('s.participant', 'organisateur' )
            ->join('s.lieu', 'lieu' )
            ->join('s.etat', 'etat' )
            ->join('s.site', 'site' )
            ->leftJoin('s.inscriptions', 'inscriptions')
            ->addSelect('organisateur')
            ->addSelect('lieu')
            ->addSelect('etat')
            ->addSelect('site')
            ->addSelect('inscriptions');
        return $res->getQuery()
                   ->getResult();
    }

    // /**
    //  * @return Sortie[] Returns an array of Sortie objects filtered by what user ask
    //  */
    public function findFilteredSorties(
        int $site,
        string $sortie,
        string $dateDebut,
        string $dateFin
        //?BooleanType $organisateur,
        //?BooleanNode $inscrit,
        //?BooleanNode $nonInscrit,
        //?BooleanNode $sortiesPassees
    ){
        $res = $this->createQueryBuilder('s')
                    ->join('s.participant', 'organisateur' )
                    ->join('s.lieu', 'lieu' )
                    ->join('s.etat', 'etat' )
                    ->join('s.site', 'site' )
                    ->addSelect('organisateur')
                    ->addSelect('lieu')
                    ->addSelect('etat')
                    ->addSelect('site');

        if($site !== 0){
            $res->andWhere('s.id_site = :site')
                ->setParameter('site', $site);
        }
        if ($sortie !== "") {
            $res->andWhere('s.nom like :sortie')
                ->setParameter('sortie', "%".$sortie."%");
        }
        if(!empty($dateDebut)){
            $res->andWhere('s.dateDebut >= :dateDebut')
                ->setParameter('dateDebut', new DateTime($dateDebut));
        }
        if(!empty($dateFin)){
            $res->andWhere('s.dateDebut <= :dateFin')
                ->setParameter('dateFin', new Datetime($dateFin));
        }/*
        if(organisateur ==""){
            $res->andWhere('s.id_organisateur = :organisateur')
                ->setParameter('organisateur', 1);
        }
        if(inscrit == ""){
            $res->andWhere('s.?? = :inscrit')
                ->setParameter('inscrit', 1);
        }
        if(nonInscrit == ""){
            $res->andWhere('s.?? = :nonInscrit')
                ->setParameter('nonInscrit', 1);
        }
        if(sortiePassees ==""){
            $res->andWhere('s.sortiePassees = :sortiePassees')
                ->setParameter('sortiePassees', 1);
        }*/
        
        return $res->getQuery()
                    ->getResult();
    }

    // /**
    //  * @return Sortie[] Returns an array of Sortie objects
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
    public function findOneBySomeField($value): ?Sortie
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
