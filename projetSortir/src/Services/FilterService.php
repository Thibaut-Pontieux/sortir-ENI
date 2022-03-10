<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\Request;

class FilterService
{

    // /**
    //  * @return Sortie[] Returns an array of Sortie objects filtered by what user ask
    //  */
    public function findFilteredSorties(Request $request){
        $site = $request->get("siteSelect");
        $sortie = $request->get("nomSortie");
        $dateDebut = $request->get("dateDebut");
        $dateFin = $request->get("dateFin");
        $organisateur = $request->get("sortiesOrganisateur");
        $inscrit = $request->get("sortiesInscrit");
        $nonInscrit = $request->get("sortiesNonInscrit");
        $sortiesPassees = $request->get("sortiesPassees");

        $res = $this->createQueryBuilder('sortie')
            ->join('sortie.participant', 'organisateur' )
            ->join('sortie.lieu', 'lieu' )
            ->join('sortie.etat', 'etat' )
            ->join('sortie.site', 'site' );

        if( $site == "0" || empty($site) &&
            empty(trim($sortie)) &&
            empty($dateDebut) &&
            empty($dateFin) &&
            empty($organisateur) &&
            empty($inscrit) &&
            empty($nonInscrit) &&
            empty($sortiesPassees)
        ){}else{
            /*
             * Champ obligatoire avec par defaut "Tous"
             * Si le "siteSelect" est différent de "Tous" (0), alors on applique une clause WHERE sur site.
             */
            if($site !== "0"){
                $res->andWhere('site.id = :site')
                    ->setParameter('site', (int)$site);
            }
            /*
             * Champ facultatif. Si l'utilisateur n'a rien saisit, la valeur sera "".
             * Si le "nomSortie" est différent de "", alors on applique une clause WHERE ... LIKE sur sortie.
             */
            if (!empty(trim($sortie))) {
                $res->andWhere('sortie.nom like :sortie')
                    ->setParameter('sortie', "%".$sortie."%");
            }
            /*
             * Champ facultatif. Si l'utilisateur n'a rien saisit, la valeur sera "".
             * Si le "dateDebut" est différent de "" (est vide), alors on applique une clause WHERE sur dateDebut.
             */
            if(!empty($dateDebut)){
                $res->andWhere('sortie.dateDebut >= :dateDebut')
                    ->setParameter('dateDebut', new DateTime($dateDebut));
            }
            /*
             * Champ facultatif. Si l'utilisateur n'a rien saisit, la valeur sera "".
             * Si le "dateFin" est différent de "" (est vide), alors on applique une clause WHERE sur dateFin.
             */
            if(!empty($dateFin)){
                $res->andWhere('sortie.dateDebut <= :dateFin')
                    ->setParameter('dateFin', new Datetime($dateFin));
            }

            /*
             * FILTRE CHECKBOX
             */
            /*
             * Champ facultatif. Si l'utilisateur n'a rien coché, la valeur n'existe pas.
             * Si le "organisateur" est egale à "" (est coché), alors on applique une clause WHERE sur id_organisateur.
             */
            if($organisateur !== null){
                $res->andWhere('organisateur = :organisateur')
                    ->setParameter('organisateur', $this->security->getUser());
            }
            /*
             * Champ facultatif. Si l'utilisateur n'a rien coché, la valeur n'existe pas.
             * Si le "inscrit" est egale à "" (est coché), alors on applique une clause WHERE sur inscription (id_sortie et id_participant).
             */
            if($inscrit !== null){

                $res->join("sortie.inscriptions", "inscription")
                    ->andWhere('inscription.participant = :participant')
                    ->setParameter('participant', $this->security->getUser());
            }
            /*
             * Champ facultatif. Si l'utilisateur n'a rien coché, la valeur n'existe pas.
             * Si le "nonInscrit" est egale à "" (est coché), alors on applique une clause WHERE sur inscription (id_sortie et id_participant).
             */
            if($nonInscrit !== null){
                $res->join("sortie.inscriptions", "inscription")
                    ->andWhere('inscription.participant != :participant')
                    ->setParameter('participant', $this->security->getUser());
            }
            /*
             * Champ facultatif. Si l'utilisateur n'a rien coché, la valeur n'existe pas.
             * Si le "sortiesPassees" est egale à "" (est coché), alors on applique une clause WHERE sur etat.
             */
            if($sortiesPassees !== null){
                $res->andWhere('etat.libelle = :sortiePassees')
                    ->setParameter('sortiePassees', "Passée");
            }
        }
        return $res->getQuery()
            ->getResult();
    }
}