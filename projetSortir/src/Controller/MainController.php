<?php

namespace App\Controller;

use App\Entity\Sites;
use App\Entity\Sorties;
use App\Repository\SitesRepository;
use App\Repository\SortiesRepository;
use Symfony\Component\Config\Definition\IntegerNode;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="app_main")
     */
    public function index(SortiesRepository $sortiesRepository, SitesRepository $sitesRepository): Response
    {
        // Si l'utilisateur n'est pas authentifié on le redirige vers la page de connexion
        if ($this->getUser() == null)
        {
            return $this->redirectToRoute('login');    
        }
        // S'il est authentifié il est alors redirigé vers la page d'accueil

        // On récupère les sites en base
        $sites = $sitesRepository->findAll();

        //On récupère toutes les sorties en base
        $sorties = $sortiesRepository->findAllSorties();
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'sorties' => $sorties,
            'sites' => $sites,
        ]);
    }
    /**
     * @Route("/filtered", name="filtered")
     */
    public function filtered(Request $request, SortiesRepository $sortiesRepository, SitesRepository $sitesRepository ): Response
    {
        // Si l'utilisateur n'est pas authentifié on le redirige vers la page de connexion
        if ($this->getUser() == null)
        {
            return $this->redirectToRoute('login');
        }
        // S'il est authentifié il est alors redirigé vers la page d'accueil

        // On récupère les résultats de la requête du formulaire
        $req = $request->request->all();

        // On récupère les sites en base
        $sites = $sitesRepository->findAll();

        dump($req);
        dump($req["nomSortie"]);





        //On récupère les sorties en fonction de de la requête
        $sorties = $sortiesRepository->findFilteredSorties(
            (int) $req["siteSelect"],
            $req["nomSortie"]
            //(date) $req[dateDebut],
            //(date) $req[dateFin],
            //$req[sortiesOrganisateur],
            //$req[sortiesInscrit],
            //$req[sortiesNonInscrit],
            //$req[sortiesPassees],

        );
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'sorties' => $sorties,
            'sites' => $sites,
        ]);
    }
}
