<?php

namespace App\Controller;

use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GestionUsersController extends AbstractController
{
    /**
     * @Route("/gestion/users", name="gestion_users")
     */
    public function index(ParticipantRepository $participantRepository): Response
    {
        $users = $participantRepository->findAll();
        return $this->render('gestion_users/index.html.twig', [
            'controller_name' => 'GestionUsersController',
            'users' => $users
        ]);
    }

    /**
     * @Route("/gestion/users/delete/{id}", name="gestion_users_delete")
     */
    public function delete(ParticipantRepository $participantRepository, EntityManagerInterface $em, int $id): Response
    {
        if ($participantRepository->find($id)){
            $user = $participantRepository->find($id);
            $em->remove($user);
            $em->flush();

            $this->addFlash("success", "Utilisateur supprimé avec succès");
        } else {
            $this->addFlash("error", "Erreur lors de la suppression d'un utilisateur");
        }
        
        return $this->redirectToRoute('gestion_users'); 
    }

    /**
     * @Route("/gestion/users/desactiver/{id}", name="gestion_users_desactiver")
     */
    public function desactiver(ParticipantRepository $participantRepository, EntityManagerInterface $em, int $id): Response
    {
        if ($participantRepository->find($id)){
            $user = $participantRepository->find($id);
            $user->setActif(!$user->getActif());
            $em->flush();

            $this->addFlash("success", sprintf("Utilisateur %s avec succès", $user->getActif() ? "activé" : "déactivé"));
        } else {
            $this->addFlash("error", "Erreur lors de la maj du statut d'un utilisateur");
        }
        
        return $this->redirectToRoute('gestion_users'); 
    }
}
