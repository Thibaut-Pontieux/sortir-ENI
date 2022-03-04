<?php

namespace App\Controller;

use App\Entity\Site;
use App\Entity\Sortie;
use App\Repository\InscriptionRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
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
    public function index(SortieRepository $sortieRepository, SiteRepository $siteRepository, ParticipantRepository $participantRepository): Response
    {
        // Si l'utilisateur n'est pas authentifié on le redirige vers la page de connexion
        if ($this->getUser() == null) {
            return $this->redirectToRoute('login');
        }

        // On récupère les sites en base
        $sites = $siteRepository->findAll();

        //On récupère toutes les sorties en base
        $sorties = $sortieRepository->findAllSorties();

        //On récupère les données de l'utilisateur connecté + celles des entités en relation avec Participant
        $user = $participantRepository->findUser($this->getUser());

        // S'il est authentifié il est alors redirigé vers la page d'accueil
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'sorties' => $sorties,
            'sites' => $sites,
            'user' => $user,
        ]);
    }

    /**
     * @Route("/filtered", name="filtered")
     */
    public function filtered(Request $request, SortieRepository $sortieRepository, SiteRepository $siteRepository, ParticipantRepository $participantRepository): Response
    {
        // Si l'utilisateur n'est pas authentifié on le redirige vers la page de connexion
        if ($this->getUser() == null) {
            return $this->redirectToRoute('login');
        }
        // S'il est authentifié il est alors redirigé vers la page d'accueil

        // On récupère les résultats de la requête du formulaire
        $req = $request->query->all();

        // On récupère les sites en base
        $sites = $siteRepository->findAll();

        //On récupère les données de l'utilisateur connecté + celles des entités en relation avec Participant
        $user = $participantRepository->findUser($this->getUser());

        //On récupère les sorties en fonction de de la requête
        $sorties = $sortieRepository->findFilteredSorties($request);
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'sorties' => $sorties,
            'sites' => $sites,
            'user' => $user,
        ]);
    }
}
