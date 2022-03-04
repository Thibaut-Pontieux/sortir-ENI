<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Site;
use App\Form\RegistrationFormType;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\FileType;


class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new Participant();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On encode le mot de passe
            $user->setMdp(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setAdministrateur(false);
            $user->setActif(true);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/register/csv", name="register_csv", methods={"GET", "POST"})
     */
    public function csv(EntityManagerInterface $em, ParticipantRepository $userRepo, UserPasswordHasherInterface $userPasswordHasher, SiteRepository $siteRepo, Request $request): Response 
    {

        $utilisateurs=[];

        //-- formulaire inscription via fichier CSV
        $form = $this->createFormBuilder()
        ->add('submitFile', FileType::class, array('label' => 'Fichier à soumettre'))
        ->getForm();

        //-- gestion du Post
        if ($request->isMethod('POST')) {

            $form->handleRequest($request);

            //-- si form soumis et valid
            if ($form->isValid() && $form->isSubmitted()) {
            
                //-- je récupère mon fichier CSV
                $file = $form->get('submitFile')->getData();

                //-- je l'ouvre
                if (($handle = fopen($file->getPathname(), "r")) !== false) {
                
                    //-- je saute la première ligne si le fichier contient un en-tête
                    $head = true;
                    //-- je lis et je traite chaque ligne
                    while (($data = fgetcsv($handle)) !== false) {
                        if ($head){
                            $head = false;
                        } else {
                            //-- j'ajouter l'utilisateur à mon tableau d'utilisateurs
                            $utilisateurs[] = $data[0];
                        }
                    }
                    fclose($handle);
                }

                //dump($utilisateurs);

                foreach ($utilisateurs as $u) {
                    //-- data = tous les champs d'un utilisateur
                    $data = explode(";", $u);

                    //-- je véridie que l'utilisateur n'existe pas
                    if (!$userRepo->findOneBy(array('pseudo' => $data[1]))){

                        //-- je crrée un utilisateur avec les données récupérées du CSV
                        $utilisateur = new Participant();
                        $utilisateur->setSite($siteRepo->find((int) $data[0]));
                        $utilisateur->setPseudo($data[1]);
                        $utilisateur->setNom($data[2]);
                        $utilisateur->setPrenom($data[3]);
                        $utilisateur->setTelephone($data[4]);
                        $utilisateur->setMail($data[5]);
                        $utilisateur->setMdp(
                            $userPasswordHasher->hashPassword(
                                    $utilisateur,
                                    $data[6]
                                )
                            );
                        $utilisateur->setAdministrateur($data[7]);
                        $utilisateur->setActif($data[8]);

                        //-- envoi en BDD
                        $em->persist($utilisateur);
                        $em->flush();
                   }
                    
                }

            }

        }

        return $this->render('registration/csv.html.twig', [
            'form' => $form->createView(),
        ]);

    }

}
