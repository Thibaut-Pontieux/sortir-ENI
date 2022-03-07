<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ProfileType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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
    public function update(Request $request, EntityManagerInterface $em, ParticipantRepository $participantRepository, UserPasswordHasherInterface $userPasswordHasherInterface, string $pseudo): Response
    {
        $user = $participantRepository->findOneBy(array('pseudo' => $pseudo));
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if (!empty($request->get('password')) && !empty($request->get('confirm')))
            {
                if ($request->get('password') == $request->get('confirm'))
                {
                    $user->setMdp(
                        $userPasswordHasherInterface->hashPassword(
                            $user,
                            $request->get('password')
                        )
                    );
                } 
                else 
                {
                    $this->addFlash('error not-matching', "Les mots de passe ne sont pas identiques");
                    return $this->redirectToRoute('updateParticipant', ['pseudo' => $user->getPseudo()]);
                }
            }
            $em->flush();
            $this->addFlash('success', "Le profil a été mis à jour");
            return $this->redirectToRoute('updateParticipant', ['pseudo' => $user->getPseudo()]);
        }

        return $this->render('participant/update.html.twig', [
            'profileForm' => $form->createView(),
        ]);
    }
}
