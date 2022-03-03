<?php

namespace App\Controller;

use App\Entity\Sorties;
use App\Repository\EtatsRepository;
use App\Repository\InscriptionRepository;
use App\Repository\LieuxRepository;
use App\Repository\ParticipantsRepository;
use App\Repository\SitesRepository;
use App\Repository\SortieRepository;
use App\Repository\VillesRepository;
use App\Entity\Sortie;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use App\Repository\VilleRepository;
use App\Services\ErrorsService;
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
    public function index(SortieRepository $sortieRepo, int $id): Response
    {  
        $sortie = $sortieRepo->find($id);
        
        return $this->render('sortie/index.html.twig', [
            'sortie' => $sortie,
        ]);
    }

    /**
     * @Route("/sortie/add", name="sortie_add", methods={"POST", "GET"})
     */
    public function add(EntityManagerInterface $em, SortieRepository $sortieRepo, ErrorsService $errorService, VilleRepository $villeRepo, LieuRepository $lieuRepo, ParticipantRepository $orgaRepo, EtatRepository $etatRepo, SiteRepository $siteRepo, Request $request): Response 
    {
        //-- initialisation du form
        $villes = $villeRepo->findAll();
        $lieux = $lieuRepo->findAll();
        $errors = [];
        $obj = [];

        $utilisateur = $this->getUser();
        
        //-- doit être connecé pour créer une sortie
        if (empty($utilisateur)){
            return $this->redirectToRoute('login');  
        }

        //-- gestion POST
        if ($request->isMethod('POST')) {
            
            $obj = $request->request->all();
            
            //-- je récupère un tableau d'erreurs (vide si il n'y en a pas)
            $errors = $errorService->getSortieErrors($obj, $villeRepo, $lieuRepo);
            
            //-- ajout de la sortie dans la bdd si aucune erreur
            if (count($errors) <= 0){
                
                $sortie = new Sortie();
                $sortie->setNom($obj["nom-sortie"]);
                $sortie->setDateDebut(new Datetime($obj["date-debut"]));
                $sortie->setDuree((int) $obj["duree"]);
                $sortie->setDateClotureInscription(new Datetime($obj["date-cloture"]));
                $sortie->setNbInscriptionsMax((int) $obj["nb-places"]);
                $obj["description"] ? $sortie->setDescriptionInfos($obj["description"]) : "";
                $sortie->setParticipant($utilisateur);
                $sortie->setLieu($lieuRepo->find($obj["lieu"]));
                $sortie->getLieu()->setVille($villeRepo->find((int) $obj["ville"]));
                //-- par défaut état = 1 ligne dans la BDD
                $sortie->setEtat($etatRepo->find(1));
                //-- par défaut ville organisatrice (-> site) = 1 ligne dans la BDD
                $sortie->setSite($siteRepo->find(1));

                $em->persist($sortie);
                $em->flush();
        
                return $this->redirectToRoute('app_main', ["obj" => $obj]);  
            }

        }
        return $this->render('sortie/form.html.twig', [
            'villes'=> $villes,
            'lieux'=> $lieux,
            "errors" => $errors,
            "obj" => $obj,
         ]);
     }

    /**
     * @Route("/sortie/modify/{id}", name="sortie_modify", methods={"POST", "GET"}, requirements={"id": "\d+"})
     */
    public function modify(EntityManagerInterface $em, SortieRepository $sortieRepo, ErrorsService $errorService, VilleRepository $villeRepo, LieuRepository $lieuRepo, ParticipantRepository $orgaRepo, EtatRepository $etatRepo, SiteRepository $siteRepo, Request $request, int $id): Response 
    {
        //-- initialisation du form
        $villes = $villeRepo->findAll();
        $lieux = $lieuRepo->findAll();
        $errors = [];
        $obj = [];
        $sortie = $sortieRepo->find($id);

        $utilisateur = $this->getUser();
        
        //-- doit être connecé et l'organisateur pour modifier une sortie
        if (empty($utilisateur)){
            return $this->redirectToRoute('login');  
        } else if ($utilisateur != $sortie->getParticipant()){
            return $this->redirectToRoute('app_main');  
        }

        //-- obj = sortie à modifier
        $obj["nom-sortie"] = $sortie->getNom();
        $obj["date-debut"] = $sortie->getDateDebut()->format('Y-m-d H:i:s');
        $obj["date-cloture"] = $sortie->getDateClotureInscription()->format('Y-m-d H:i:s');
        $obj["nb-places"] = $sortie->getNbInscriptionsMax();
        $obj["duree"] = $sortie->getDuree();
        $obj["description"] = $sortie->getDescriptionInfos();
        $obj["ville"] = $sortie->getLieu()->getVille()->getId();
        $obj["lieu"] = $sortie->getLieu()->getId();

        //-- gestion du POST
        if ($request->isMethod('POST')) {
            
            $obj = $request->request->all();
            
            //-- je récupère un tableau d'erreurs (vide si il n'y en a pas)
            $errors = $errorService->getSortieErrors($obj, $villeRepo, $lieuRepo);
            
             //-- modification de la sortie dans la bdd si aucune erreur
            if (count($errors) <= 0){
                
                $sortie = $sortieRepo->find($id);
                $sortie->setNom($obj["nom-sortie"]);
                $sortie->setDateDebut(new Datetime($obj["date-debut"]));
                $sortie->setDuree((int) $obj["duree"]);
                $sortie->setDateClotureInscription(new Datetime($obj["date-cloture"]));
                $sortie->setNbInscriptionsMax((int) $obj["nb-places"]);
                $obj["description"] ? $sortie->setDescriptionInfos($obj["description"]) : "";
                $sortie->setLieu($lieuRepo->find($obj["lieu"]));
                $sortie->getLieu()->setVille($villeRepo->find((int) $obj["ville"]));

                $em->flush();
        
                return $this->redirectToRoute('app_main', ["obj" => $obj]);  
            }
        }
         
        return $this->render('sortie/form.html.twig', [
            'villes'=> $villes,
            'lieux'=> $lieux,
            "errors" => $errors,
            "obj" => $obj,
            "id" => $id
         ]);       
     }

    /**
     * @Route("/sortie/cancel/{id}", name="sortie_cancel", methods={"GET"}, requirements={"id": "\d+"})
     */
    public function cancel(EntityManagerInterface $em, SortieRepository $sortieRepo, EtatRepository $etatRepo, int $id): Response
    {  
        $sortie = $sortieRepo->find($id);

        if (!empty($sortie)){
            //-- si la sortie n'est pas commencée et que je suis l'organisateur, je peux l'annuler
            if ($sortie->getDateDebut() > new DateTime() && $sortie->getParticipant() == $this->getUser()){
                $etat = $etatRepo->findOneBy(array('libelle' => 'Annulée'));
                $sortie->setEtat($etat);
                $em->flush();
            }
            
            // TODO: gérer les messages flash
        }
        
        return $this->render('sortie/index.html.twig', [
            'sortie' => $sortie,
        ]);
    }
}


