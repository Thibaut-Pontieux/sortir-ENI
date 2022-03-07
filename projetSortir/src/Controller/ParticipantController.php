<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ProfileType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ParticipantController extends AbstractController
{
    /**
     * @Route("/participant/{pseudo}", name="participant", methods={"GET"})
     */
    public function index(ParticipantRepository $participantRepository, string $pseudo): Response
    {
        $participant = $participantRepository->findOneBy(array('pseudo' => $pseudo));
        return $this->render('participant/index.html.twig', [
            'controller_name' => 'ParticipantController',
            'participant' => $participant
        ]);
    }

    /**
     * @Route("/participant/update/{pseudo}", name="updateParticipant")
     */
    public function update(Request $request, EntityManagerInterface $em, ParticipantRepository $participantRepository, string $pseudo): Response
    {
        $user = $participantRepository->findOneBy(array('pseudo' => $pseudo));
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            dump($user);
            $em->flush();
        }

        return $this->render('participant/update.html.twig', [
            'profileForm' => $form->createView(),
        ]);
    }
}
