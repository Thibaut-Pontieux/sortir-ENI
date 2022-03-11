<?php

namespace App\Controller;

use App\Entity\Site;
use App\Entity\Ville;
use App\Form\SiteType;
use App\Repository\SiteRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GestionSitesController extends AbstractController
{
    /**
     * @Route("/gestion/sites/{id}", name="gestion_sites")
     */
    public function index(
        $id = null,
        Request $request,
        EntityManagerInterface $entityManager,
        SiteRepository $siteRepository,
        VilleRepository $villeRepository): Response
    {
        /*
         * Récuperation de la session
         */
        $session = $request->getSession();

        $site = null;
        if($id) {
            /*
           * Récupération d'un site en fonction de l'id placé en paramètre.
           * Si id = null, alors aucun site n'est récupéré.
           */
            $site = $siteRepository->find($id);
        }

        /*
         * Si une ville est récupérée (n'est pas null), modify = true, sinon false.
         * Si modify = false, on créer une nouvelle ville
         */
        $modify = isset($site);
        if (!$modify) {
            $site = new Site();
        }

        /*
         * Les villes à afficher dans liste déroulante (toutes)
         */
        $villes = $villeRepository->findAll();

        /*
        * Création du formulaire à partir de la classe SiteType, avec pour donnée le site (soit nouveau, soit récupéré).
        */
        $siteForm = $this->createForm(SiteType::class, $site);

        /*
        * Si le formulaire à été submit et qu'il est valide : on pousse en base puis redirige vers la page de gestion des sites.
        * Sinon, on retourne le visuel avec le formulaire.
        */
        $siteForm->handleRequest($request);
        if ($siteForm->isSubmitted() && $siteForm->isValid()) {

            try {
                $entityManager->persist($site);
                $entityManager->flush();
            } catch (\Exception $e) {
                $this->addFlash("error", "Une erreur est survenue. $e");
            }finally {
                $nom = $site->getNom();
                $this->addFlash("success", "site \"$nom\" ajoutée.");
            }

            return $this->redirectToRoute('gestion_sites');
        }

        /*
         * Récupération des valeurs du champ de saisie du filtre
         */
        $filtreVille = $request->get('filtreVille');
        $filtreSite = $request->get('filtreSite');

        /*
         * Sauvegarde en session
         */
        $session->set('filtreSite',$filtreSite);
        $session->set('filtreVille',$filtreVille);

        /*
         * Affichage des sites en fonction du filtre
         */
        $sites = null;
        //Si pas d'identifiant, ni de filtre ou alors qu'identifiant et filtre soient vide
        if((empty($filtreVille) and empty($filtreSite))){
            $sites = $siteRepository->findAll();
        }else{
            /* Paramètre de la requête */
            $sites = $siteRepository->findFilteredSites($filtreSite, $filtreVille);
        }

        return $this->render('gestion_sites/index.html.twig', [
            'controller_name' => 'GestionSitesController',
            'sites' => $sites,
            'villes' => $villes,
            'site' => $site,
            'siteView' => $siteForm->createView(),
            'filtreSite' => $session->get('filtreSite'),
            'filtreVille' =>$session->get('filtreVille')
        ]);
    }

    /**
     * @Route("/gestion/sites/{id}/delete", name="gestion_sites_delete", methods={"DELETE"}, requirements={"id": "\d+"})
     * @throws ORMException
     */
    public function delete(Site $site, EntityManagerInterface $entityManager): Response
    {
        $nomSite = $site->getNom();
        try {
            $entityManager->remove($site);
            $entityManager->flush();
            $entityManager->detach($site);
        } catch (OptimisticLockException $e) {
            $this->addFlash("error", "Une erreur est survenue. $e");
        }finally {
            $nom = $site->getNom();
            $this->addFlash("success", "Site \"$nom\" supprimée.");
        }

        return $this->redirectToRoute('gestion_sites');
    }
}
