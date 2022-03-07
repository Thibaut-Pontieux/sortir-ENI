<?php

namespace App\Controller;

use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GestionVillesController extends AbstractController
{
    /**
     * @Route("/gestion/villes", name="gestion_villes")
     */
    public function index( VilleRepository $villeRepository): Response
    {
        $villes = $villeRepository->findAll();
        return $this->render('gestion_villes/index.html.twig', [
            'controller_name' => 'GestionVillesController',
            'villes' => $villes
        ]);
    }
}
