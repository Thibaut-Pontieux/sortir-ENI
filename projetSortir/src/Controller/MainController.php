<?php

namespace App\Controller;

use App\Entity\Sorties;
use App\Repository\SortiesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="app_main")
     */
    public function index(SortiesRepository $sortiesRepository): Response
    {
        // Si l'utilisateur n'est pas authentifié on le redirige vers la page de connexion
        if ($this->getUser() == null)
        {
            return $this->redirectToRoute('login');    
        }
        // S'il est authentifié il est alors redirigé vers la page d'accueil
        $sorties = $sortiesRepository->findAllSorties();
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'sorties' => $sorties
        ]);
    }
}
