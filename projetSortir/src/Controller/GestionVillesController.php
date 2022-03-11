<?php

namespace App\Controller;

use App\Form\VilleType;
use App\Repository\VilleRepository;
use App\Entity\Ville;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\BooleanNode;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GestionVillesController extends AbstractController
{
    /**
     * @Route("/gestion/villes/{id}", name="gestion_villes")
     * @throws ORMException
     */
    public function index(
        $id = null,
        EntityManagerInterface $entityManager,
        VilleRepository $villeRepository,
        Request $request): Response
    {
        /*
         * Récuperation de la session
         */
        $session = $request->getSession();

        $ville = null;
        if ($id) {
            /*
             * Récupération d'une ville en fonction de l'id placé en paramètre.
             * Si id = null, alors aucune ville n'est récupérée.
             */
            $ville = $villeRepository->find($id);
        }
        /*
         * Si une ville est récupérée (n'est pas null), modify = true, sinon false.
         * Si modify = false, on créer une nouvelle ville
         */
        $modify = isset($ville);
        if (!$modify) {
            $ville = new Ville();
        }

        /*
         * Création du formulaire à partir de la classe VilleType, avec pour donnée la ville (soit nouvelle, soit récupérée).
         * Possibilité de rajouter une option, cf exemple en commentaire.
         * Si modify = true (modification d'une ville) alors, "isCPEditable" = false --> on n'édite pas le CP
         * Si modify = false (création d'une ville) alors, "isCPEditable" = true --> on édite le CP
         */
        //$villeForm = $this->createForm(VilleType::class, $ville, ['isCPEditable' => !$modify]);
        $villeForm = $this->createForm(VilleType::class, $ville);

        /*
         * Si le formulaire à été submit et qu'il est valide : on pousse en base puis redirige vers la page de gestion des villes.
         * Sinon, on retourne le visuel avec le formulaire.
         */
        $villeForm->handleRequest($request);
        if ($villeForm->isSubmitted() && $villeForm->isValid()) {
            try {
                $entityManager->persist($ville);
                $entityManager->flush();
            } catch (OptimisticLockException $e) {
                $this->addFlash("error", "Une erreur est survenue. $e");
            } finally {
                $nom = $ville->getNom();
                $cp = $ville->getCp();
                $this->addFlash("success", "Ville \"$nom\" ($cp) ajoutée.");
            }
            return $this->redirectToRoute('gestion_villes');
        }

        /*
         * Récupération de la valeur du champ de saisie du filtre
         */
        $filtreNom = $request->get('ville') ?? '';

        /*
         * Sauvegarde en session
         */
        $session->set('filtreNom',$filtreNom);

        /*
         * Récupération des villes en base
         */
        $villes = $villeRepository->findFilteredVilles($filtreNom);

        return $this->render('gestion_villes/index.html.twig', [
            'villes' => $villes,
            'ville' => $ville,
            'modify' => $modify,
            'villeView' => $villeForm->createView(),
            'filtreNom' => $session->get('filtreNom')
        ]);
    }

    /**
     * @Route("/gestion/villes/{id}/delete", name="gestion_villes_delete", methods={"DELETE"}, requirements={"id": "\d+"})
     * @throws ORMException
     */
    public function delete(Ville $ville, EntityManagerInterface $entityManager): Response
    {
        try {
            $entityManager->remove($ville);
            $entityManager->flush();
            $entityManager->detach($ville);
        } catch (OptimisticLockException $e) {
            $this->addFlash("error", "Une erreur est survenue. $e");
        } finally {
            $nom = $ville->getNom();
            $cp = $ville->getCp();
            $this->addFlash("success", "Ville \"$nom\" ($cp) supprimée.");
        }

        return $this->redirectToRoute('gestion_villes');
    }
}
