<?php

namespace App\Controller;

use App\Entity\Inscriptions;
use App\Entity\Participants;
use App\Repository\InscriptionsRepository;
use App\Repository\ParticipantsRepository;
use App\Repository\SortiesRepository;
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
     * @Route("/inscrire/add", name="add_participation")
     */
    public function participer(EntityManagerInterface $em, ParticipantsRepository $participant, SortiesRepository $sorties, InscriptionsRepository $inscriptions): Response
    {
        $i = new Inscriptions();
        $u = $this->getUser();
        if (empty($u))
        {
            $this->addFlash("error", "Veuillez vous authentifier avant d'essayer de vous inscrire à une sortie");
        }
        else {
            $user = $participant->findOneBy(array('pseudo' => $u->getUserIdentifier()));
            $sortie = $sorties->find(1);
            $dejaInscrit = $inscriptions->findOneBy(array('id_sortie' => 1, 'id_participant' => $user->getId()));

            // Si l'utilisateur est déjà inscrit alors on ne l'inscrit pas une deuxième fois
            if (!empty($dejaInscrit))
            {
                $this->addFlash("error", "Vous êtes déjà inscrit pour cette sortie");
                return $this->render('inscrire/index.html.twig', [
                    'controller_name' => 'InscrireController',
                ]);
            }

            // Initilisation de l'inscription
            $i->setIdParticipant($user);
            $i->setIdSortie($sortie);
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
        return $this->render('inscrire/index.html.twig', [
            'controller_name' => 'InscrireController',
        ]);
    }

    /**
     * @Route("/inscrire/remove", name="remove_participation")
     */
    public function desister(EntityManagerInterface $em, ParticipantsRepository $participant, SortiesRepository $sorties, InscriptionsRepository $inscriptions): Response 
    {
        $u = $this->getUser();
        if (empty($u))
        {
            $this->addFlash("error", "Veuillez vous authentifier avant d'essayer de vous inscrire à une sortie");
        }
        $user = $participant->findOneBy(array('pseudo' => $u->getUserIdentifier()));
        $sortie = $sorties->find(1);
        $estInscrit = $inscriptions->findOneBy(array('id_sortie' => 1, 'id_participant' => $user->getId()));

        if (!empty($estInscrit))
        {
            $em->remove($estInscrit);
            $em->flush();
            $this->addFlash("success", "Vous ne participez plus à cette sortie");
        }
        else {
            $this->addFlash("error", "Vous ne participez pas à cette sortie");
        }

        return $this->render('inscrire/index.html.twig', [
            'controller_name' => 'InscrireController',
        ]);
    }

}
