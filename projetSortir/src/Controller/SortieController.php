<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use App\Repository\VilleRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/sortie/add", name="sortie", methods={"GET"})
     */
    public function index(VilleRepository $villeRepo, LieuRepository $lieuRepo): Response
    {   //-- initialisation du form
        $villes = $villeRepo->findAll();
        $lieux = $lieuRepo->findAll();
        
        return $this->render('sortie/index.html.twig', [
            'villes'=>$villes,
            'lieux'=>$lieux,
            'errors' => isset($_GET["errors"]) ? $_GET["errors"] : [],
            'obj' => isset($_GET["obj"]) ? $_GET["obj"] : []
        ]);
    }

      /**
     * @Route("/sortie/add", name="sortie_add", methods={"POST"})
     */
    public function add(EntityManagerInterface $em, VilleRepository $villeRepo, LieuRepository $lieuRepo, ParticipantRepository $orgaRepo, EtatRepository $etatRepo, SiteRepository $siteRepo, Request $request): Response {
        
        $obj = $request->request->all();

        //-- gestion ds erreurs du form
        $errors = [];

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
            if ($lieuRepo->find((int) $obj["lieu"])->getVille() != $villeRepo->find((int) $obj["ville"])){
                $errors[] = "Ville et lieu incompatibles";
            }
        }
        
         //-- ajout de la sortie dans la bdd si aucune erreur
        if (count($errors) <= 0){
            $sortie = new Sortie();
            $sortie->setNom($obj["nom-sortie"]);
            $sortie->setDateDebut(new Datetime($obj["date-debut"]));
            $sortie->setDuree((int) $obj["duree"]);
            $sortie->setDateClotureInscription(new Datetime($obj["date-cloture"]));
            $sortie->setNbInscriptionsMax((int) $obj["nb-places"]);
            $obj["description"] ? $sortie->setDescriptionInfos($obj["description"]) : "";
            //-- par défaut organisateur = 1 ligne dans la BDD
            $sortie->setParticipant($orgaRepo->find(1));
            $sortie->setLieu($lieuRepo->find($obj["lieu"]));
            //-- par défaut état = 1 ligne dans la BDD
            $sortie->setEtat($etatRepo->find(1));
            //-- par défaut ville organisatrice (-> site) = 1 ligne dans la BDD
            $sortie->setSite($siteRepo->find(1));

            $em->persist($sortie);
            $em->flush();

            return $this->redirectToRoute('app_main', ["obj" => $obj]);  
        }
    
        return $this->redirectToRoute('sortie', ["errors" => $errors, "obj" => $obj]);  
        
    }
}