<?php

namespace App\Controller;

use App\Repository\SiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GestionSitesController extends AbstractController
{
    /**
     * @Route("/gestion/sites", name="gestion_sites")
     */
    public function index(SiteRepository $siteRepository): Response
    {
        $sites = $siteRepository->findAll();
        return $this->render('gestion_sites/index.html.twig', [
            'controller_name' => 'GestionSitesController',
            'sites' => $sites
        ]);
    }
}
