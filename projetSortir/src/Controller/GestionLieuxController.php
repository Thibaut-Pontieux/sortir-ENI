<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GestionLieuxController extends AbstractController
{
    /**
     * @Route("/gestion/lieux/{id}", name="gestion_lieux")
     */
    public function index(
        $id = null,
        EntityManagerInterface $entityManager,
        Request $request,
        LieuRepository $lieuRepository,
        VilleRepository $villeRepository
    ): Response
    {
        $lieu = null;
        if($id){
            /*
             * Récupération d'un lieu en fonction de l'id placé en paramètre.
             * Si id = null, alors aucun lieu n'est récupéré.
             */
            $lieu = $lieuRepository->find($id);
        }

        /*
         * Si un lieu est récupérée (n'est pas null), modify = true, sinon false.
         * Si modify = false, on créer un nouveau lieu
         */
        $modify = isset($lieu);
        if (!$modify) {
            $lieu = new Lieu();
        }

        /*
         * Création du formulaire à partir de la classe LieuType.
         */
        $lieuForm = $this->createForm(LieuType::class, $lieu);

        /*
         * Si le formulaire à été submit et qu'il est valide :
         * On pousse en base puis redirige vers la page de gestion des lieux.
         * Sinon, on retourne le visuel avec le formulaire.
         */
        $lieuForm->handleRequest($request);
        if($lieuForm->isSubmitted() && $lieuForm->isValid()){
            try {
                $entityManager->persist($lieu);
                $entityManager->flush();
            } catch (OptimisticLockException $e){
                $this->addFlash("error", "Une erreur est survenue. $e");
            } finally {
                $this->addFlash("success", "Nouveau lieu ajouté");
            }
            return  $this->redirectToRoute('gestion_lieux');
        }

        /*
         * Récupération des villes en base
         */
        $villes = $villeRepository->findAll();

        /*
         * Récupération des valeurs du champ de saisie du filtre
         */
        $idVille = $request->get('ville');
        $filtreLieu = $request->get('lieu') ?? '';

        /*
         * Affichage des lieu en fonction du filtre
         */
        //Si pas d'identifiant, ni de filtre ou alors qu'identifiant et filtre soient vide
        if((empty($idVille) and empty($filtreLieu))){
            $lieux = $lieuRepository->findAll();
        }else{
            /* Paramètre de la requête */
            $lieux = $lieuRepository->findFilteredLieux($filtreLieu, $idVille);
        }


        return $this->render('gestion_lieux/index.html.twig', [
            'controller_name' => 'GestionLieuxController',
            'villes' => $villes,
            'lieux' => $lieux,
            'lieu' => $lieu,
            'modify' => $modify,
            'lieuView'=> $lieuForm->createView()
        ]);
    }

    /**
     * @Route("/gestion/lieux/{id}/delete", name="gestion_lieux_delete", methods={"DELETE"}, requirements={"id": "\d+"})
     * @throws ORMException
     */
    public function delete(Lieu $lieu, EntityManagerInterface $entityManager): Response
    {
        try {
            $entityManager->remove($lieu);
            $entityManager->flush();
            $entityManager->detach($lieu);
        } catch (OptimisticLockException $e) {
            $this->addFlash("error", "Une erreur est survenue. $e");
        } finally {
            $nom = $lieu->getNom();
            $this->addFlash("success", "Lieu \"$nom\" supprimée.");
        }

        return $this->redirectToRoute('gestion_lieux');
    }
}
