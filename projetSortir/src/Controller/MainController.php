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
    public function index(Request $request, SortieRepository $sortieRepository, SiteRepository $siteRepository, ParticipantRepository $participantRepository): Response
    {
        // Si l'utilisateur n'est pas authentifié on le redirige vers la page de connexion
        if ($this->getUser() == null) {
            return $this->redirectToRoute('login');
        }
        // On récupère la date d'aujourd'hui
        $date = (new \DateTime('now'))->format('d/m/Y');

        // On récupère les sites en base
        $sites = $siteRepository->findAll();

        //On récupère toutes les sorties en base
        $sorties = $sortieRepository->findFilteredSorties($request);

        $user = $participantRepository->findUser($this->getUser());
        // S'il est authentifié il est alors redirigé vers la page d'accueil
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'currentDate' => $date,
            'sorties' => $sorties,
            'sites' => $sites,
            'user' => $user,
        ]);
    }
}
