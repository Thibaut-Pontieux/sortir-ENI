<?php

namespace App\Controller;

use App\Entity\Sorties;
use App\Repository\EtatsRepository;
use App\Repository\InscriptionsRepository;
use App\Repository\LieuxRepository;
use App\Repository\ParticipantsRepository;
use App\Repository\SitesRepository;
use App\Repository\SortiesRepository;
use App\Repository\VillesRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/sortie/{id}", name="sortie", methods={"GET"}, requirements={"id": "\d+"})
     */
    public function index(SortiesRepository $sortieRepo, InscriptionsRepository $inscritRepo, int $id): Response
    {  
        $sortie = $sortieRepo->findSortie($id);
        $incrits = $inscritRepo->findInscrits($id);
        
        // var_dump($sortie);
        // var_dump($incrits);

        return $this->render('sortie/index.html.twig', [
            'sortie' => $sortie,
            'incrits' => $incrits
        ]);
    }

      /**
     * @Route("/sortie/add", name="sortie_add", methods={"POST", "GET"})
     */
    public function add(EntityManagerInterface $em, VillesRepository $villeRepo, LieuxRepository $lieuRepo, ParticipantsRepository $orgaRepo, EtatsRepository $etatRepo, SitesRepository $siteRepo, Request $request): Response 
    {
        //-- initialisation du form
        $villes = $villeRepo->findAll();
        $lieux = $lieuRepo->findAll();
        $errors = [];
        //-- obj = une sortie
        $obj = [];
        
        //-- gestion POST
        if ($request->isMethod('POST')) {
            $obj = $request->request->all();

            //-- gestion ds erreurs du form
            if ($obj["nom-sortie"] == ""){
                $errors[] = "Nom incorrect";
            }
    
            if ($obj["date-debut"] == ""){
                $errors[] = "Date de début de la sortie incorrecte";
            }
    
            if ($obj["date-cloture"] == ""){
                $errors[] = "Date limite d'inscription incorrecte";
            }
    
            if ($obj["nb-places"] == "" || (int) $obj["nb-places"] <= 0){
                $errors[] = "Nombre de places incorrect";
            }
    
            if ($obj["duree"] == "" || (int) $obj["duree"] <= 0){
                $errors[] = "Durée incorrecte";
            }
    
            if ($obj["lieu"] == "no"){
                $errors[] = "Lieu incorrect";
            } 
            else if ( $obj["ville"] == "no"){
                $errors[] = "Ville incorrecte";
            }else if ($obj["ville"] != "no" && $obj["ville"] != "no"){
                if ($lieuRepo->find((int) $obj["lieu"])->getIdVille() != $villeRepo->find((int) $obj["ville"])){
                    $errors[] = "Ville et lieu incompatibles";
                }
            }
            
             //-- ajout de la sortie dans la bdd si aucune erreur
            if (count($errors) <= 0){
                $sortie = new Sorties();
                $sortie->setNom($obj["nom-sortie"]);
                $sortie->setDateDebut(new Datetime($obj["date-debut"]));
                $sortie->setDuree((int) $obj["duree"]);
                $sortie->setDateClotureInscription(new Datetime($obj["date-cloture"]));
                $sortie->setNbInscriptionsMax((int) $obj["nb-places"]);
                $obj["description"] ? $sortie->setDescriptionInfos($obj["description"]) : "";
                //-- par défaut organisateur = 1 ligne dans la BDD
                $sortie->setIdOrganisateur($orgaRepo->find(1));
                $sortie->setIdLieu($lieuRepo->find($obj["lieu"]));
                //-- par défaut état = 1 ligne dans la BDD
                $sortie->setIdEtat($etatRepo->find(1));
                //-- par défaut ville organisatrice (-> site) = 1 ligne dans la BDD
                $sortie->setIdSite($siteRepo->find(1));
    
                $em->persist($sortie);
                $em->flush();
    
                return $this->redirectToRoute('app_main', ["obj" => $obj]);  
            }
        }
         
        return $this->render('sortie/add.html.twig', [
            'villes'=> $villes,
            'lieux'=> $lieux,
            "errors" => $errors,
            "obj" => $obj
         ]);
        
     }
}


