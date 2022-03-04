<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Repository\ParticipantRepository;
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
}
