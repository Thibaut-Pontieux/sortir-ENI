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
        $user = $this->getUser();
        $users = $participantRepository->findAll();
        return $this->render('gestion_users/index.html.twig', [
            'controller_name' => 'GestionUsersController',
            'users' => $users,
            'userConnected' => $user
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

    /**
     * @Route("/gestion/users/changeAdmin/{id}", name="gestion_users_changeAdmin")
     */
    public function changeAdmin(int $id, ParticipantRepository $participantRepository, EntityManagerInterface $em): Response
    {
        $user = $participantRepository->find($id);
        if ($user){
            if($user->getAdministrateur()){
                $user->setAdministrateur(false);
                $user->setRoles(['ROLE_USER']);
            }else{
                $user->setAdministrateur(true);
                $user->setRoles(['ROLE_ADMIN']);
            }
            $em->flush();

            $this->addFlash("success", sprintf("Utilisateur %s promu au rang de %s", $user->getPseudo(), $user->getAdministrateur() ? "admin" : "utilisateur"));
        } else {
            $this->addFlash("error", "Erreur lors de la promotion d'un utilisateur");
        }
        return $this->redirectToRoute('gestion_users');
    }
}
