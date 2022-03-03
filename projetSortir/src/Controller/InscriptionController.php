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
        }
        else {
            $user = $participant->findOneBy(array('pseudo' => $u->getUserIdentifier()));
            $sortie = $sorties->find($idSortie);
            $dejaInscrit = $inscriptions->findOneBy(array('sortie' => $idSortie, 'participant' => $user->getId()));

            // Si l'utilisateur est déjà inscrit alors on ne l'inscrit pas une deuxième fois
            if (!empty($dejaInscrit))
            {
                $this->addFlash("error", "Vous êtes déjà inscrit pour cette sortie");
                return $this->redirectToRoute('app_main');
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
        }
        return $this->redirectToRoute('app_main');
    }

    /**
     * @Route("/inscrire/remove/{idSortie}", name="remove_participation")
     */
    public function desister($idSortie, EntityManagerInterface $em, InscriptionRepository $inscriptions): Response 
    {
        $u = $this->getUser();
        if (empty($u))
        {
            $this->addFlash("error", "Veuillez vous authentifier avant d'essayer de vous inscrire à une sortie");
            return $this->redirectToRoute('app_main');
        }
        $estInscrit = $inscriptions->findOneBy(array('sortie' => $idSortie, 'participant' => $u));

        if (!empty($estInscrit))
        {
            $em->remove($estInscrit);
            $em->flush();
            $this->addFlash("success", "Vous ne participez plus à cette sortie");
        }
        else {
            $this->addFlash("error", "Vous ne participez pas à cette sortie");
        }

        return $this->redirectToRoute('app_main');
    }

}
