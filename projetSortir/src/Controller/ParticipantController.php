<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ProfileType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;


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

    /**
     * @Route("/participant/upload/{pseudo}", name="upload")
     */
    public function upload(Request $request, EntityManagerInterface $em, ParticipantRepository $participantRepository, UserPasswordHasherInterface $userPasswordHasherInterface, string $pseudo): Response
    {
        $user = $participantRepository->findOneBy(array('pseudo' => $pseudo));
        
        $extensions = ["gif", "jpg", "jpeg", "png"];

        $form = $this->createFormBuilder()
        ->add('attachment', FileType::class, array('label' => 'Photo à ajouter'))
        ->getForm();

        if ($request->isMethod('POST')) {

            $form->handleRequest($request);

            //-- si form soumis et valid
            if ($form->isValid() && $form->isSubmitted()) {

                //-- on récupère le fichier soumis via le form
                $uploadedFile = $form->get('attachment')->getData();

                //-- on vérifie qu'il s'agit d'une image

                if(!in_array($uploadedFile->getClientOriginalExtension(), $extensions)){
                  
                    $this->addFlash('error', "Le fichier demandé doit être une image");

                } else {
                     //-- on récupère le dossier images dans public/images
                    $destination = $this->getParameter('kernel.project_dir').'/public/images';  

                    //-- on modifie le nom de l'image récupérée afin qu'elle soit unique en ajoutant pseudo-nomDuFichier au début du fichier
                    $fileWithNewName = $user->getId()."-".$uploadedFile->getClientOriginalName();
                
                    //dump($fileWithNewName);
                    
                    //-- on la déplace dans le dossier images
                    $uploadedFile->move($destination, $fileWithNewName);
                    
                    //-- si l'utilisateur avait déja une photo de profil
                    if ($user->getUrlPhoto()){
                        $filesystem = new Filesystem();
                        //-- on supprime l'ancienne photo du dossier public
                        $filesystem->remove($destination.'/'.$user->getUrlPhoto());
                    }

                    //-- on sauvegarde le nom de la photo en bdd
                    $user->setUrlPhoto($fileWithNewName);
                    $em->flush();

                    return $this->redirectToRoute('participant', ['pseudo' => $user->getPseudo()]);
                }        
            }
        }

        return $this->render('participant/upload.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
