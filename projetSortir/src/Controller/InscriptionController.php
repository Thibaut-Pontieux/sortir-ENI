<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Entity\Participant;
use App\Repository\InscriptionRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InscriptionController extends AbstractController
{
    /**
     * @Route("/inscrire", name="inscrire")
     */
    public function index(): Response
    {
        return $this->render('inscrire/index.html.twig', [
            'controller_name' => 'InscrireController',
        ]);
    }

    /**
     * @Route("/inscrire/add/{idSortie}", name="add_participation")
     */
    public function participer($idSortie, EntityManagerInterface $em, ParticipantRepository $participant, SortieRepository $sorties, InscriptionRepository $inscriptions): Response
    {
        $i = new Inscription();
        $u = $this->getUser();
        
        if (empty($u))
        {
            $this->addFlash("error", "Veuillez vous authentifier avant d'essayer de vous inscrire à une sortie");
            return $this->redirectToRoute('app_main');
        }
        
        $user = $participant->findOneBy(array('pseudo' => $u->getUserIdentifier()));
        $sortie = $sorties->find($idSortie);

        // On vérifie s'il reste de la place pour s'inscrire
        if(count($sortie->getInscriptions()) == $sortie->getNbInscriptionsMax())
        {
            $this->addFlash("error", "Il n'y a plus de place");
            return $this->redirectToRoute('app_main');
        }
            
        // On vérifie si l'utilisateur n'est pas déjà inscrit à la sortie
        foreach ($sortie->getInscriptions() as $insc)
        {
            if ($insc->getParticipant() == $user)
            {
                $this->addFlash("error", "Vous êtes déjà inscrit pour cette sortie");
                return $this->redirectToRoute('app_main');
            }
        }

        // Initilisation de l'inscription
        $i->setParticipant($user);
        $i->setSortie($sortie);
        $i->setDate(new \DateTime());

        // Si la date de cloture est dépassée on ne peut plus s'inscrire
        if ($sortie->getDateClotureInscription() < $i->getDate())
        {
            $this->addFlash("error", "La date de clôture est passée, impossible de s'inscrire");
        }
        else {
            $em->persist($i);
            $em->flush();
            $this->addFlash("success", "L'inscription a bien été prise en compte");
        }

        return $this->redirectToRoute('app_main');
    }

    /**
     * @Route("/inscrire/remove/{idSortie}", name="remove_participation")
     */
    public function desister($idSortie, EntityManagerInterface $em, ParticipantRepository $participantRepository): Response 
    {
        $u = $this->getUser();

        if (empty($u))
        {
            $this->addFlash("error", "Veuillez vous authentifier avant d'essayer de vous inscrire à une sortie");
            return $this->redirectToRoute('app_main');
        }
        
        $user = $participantRepository->find($u);
                
        // On vérifie si l'utilisateur n'est pas déjà inscrit à la sortie
        foreach ($user->getInscriptions() as $insc)
        {
            if ($insc->getSortie()->getId() == $idSortie)
            {
                $em->remove($insc);
                $em->flush();
                $this->addFlash("success", "Vous ne participez plus à cette sortie");
                return $this->redirectToRoute('app_main');
            }
        }
        
        $this->addFlash("error", "Vous ne participez pas à cette sortie");
        return $this->redirectToRoute('app_main');
    }

}
