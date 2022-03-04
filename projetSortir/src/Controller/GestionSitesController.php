<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GestionSitesController extends AbstractController
{
    /**
     * @Route("/gestion/sites", name="gestion_sites")
     */
    public function index(): Response
    {
        return $this->render('gestion_sites/index.html.twig', [
            'controller_name' => 'GestionSitesController',
        ]);
    }
}
