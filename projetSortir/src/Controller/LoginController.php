<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\RegistrationFormType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
     /**
     * @Route("/login", name="login")
     */
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($error != null)
        {
            $this->addFlash("error", 'Identifiant ou mot de passe incorrect');
        }
        
        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
        ]);
    }
    
    /**
     * @Route("/logout", name="logout")
     */
    public function logout(): Response
    {
        throw new \Exception('Ne pas oublier d\'activer la deconnexion dans security.yaml');
    }

    /**
     * @Route("/reset", name="reset")
     */
    public function reset(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, ParticipantRepository $userRepo): Response
    {   
        $mail = null;
        $pseudo = null;

        //-- si le form est soumis
        if ($request->isMethod('POST')) {
            
            //-- on récupère les données
            $mail = $request->request->get('reset_m');
            $pseudo = $request->request->get('reset_pseudo');
            $password1 = $request->request->get('reset_p1');
            $password2 = $request->request->get('reset_p2');

            //dump($mail,$pseudo,$password1,$password2);
                 
            //-- on vérifie que l'utilisateur existe
            if ($userRepo->findOneBy(array('mail' => $mail, 'pseudo' => $pseudo )) != null){

                //-- on vérifie que les deux mdp saisis sont identiques
                if($password1 == $password2){
                    //-- on modifie le mdp
                    $user = $userRepo->findOneBy(array('mail' => $mail, 'pseudo' => $pseudo ));
                    $user->setMdp(
                        $userPasswordHasher->hashPassword(
                                $user,
                                $password1
                            )
                        );
                    
                    //-- on maj en BDD
                    $entityManager->flush();
                    
                    $this->addFlash("success", "Le mot de passe à bien été ré-initialiser");

                    return $this->redirectToRoute('login');
                } else {
                    $this->addFlash("error", "Les mots de passe ne sont pas identiques");
                }

            } else {
                $this->addFlash("error", "Email ou pseudo inccorect");
            } 
        }

        return $this->render('login/reset.html.twig', ['mail' => $mail, 'pseudo' => $pseudo]);
    }


}
