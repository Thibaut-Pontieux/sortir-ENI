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
        $sorties = $sortiesRepository->findAllSorties();
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'sorties' => $sorties
        ]);
    }
}
