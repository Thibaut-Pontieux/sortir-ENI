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
        VilleRepository $villeRepository, Request $request): Response
    {
        /*
         * Récupération des villes en base
         */
        $villes = $villeRepository->findAll();

        /*
         * Récupération d'une ville en fonction de l'id placé en paramètre.
         * Si id = null, alors aucune ville n'est récupérée.
         */
        $ville = $villeRepository->findOneBy(array('id' => $id));

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
            $entityManager->persist($ville);
            $entityManager->flush();

            return $this->redirectToRoute('gestion_villes');
        }

        return $this->render('gestion_villes/index.html.twig', [
            'villes' => $villes,
            'ville' => $ville,
            'modify' => $modify,
            'villeView' => $villeForm->createView(),
        ]);
    }

    /**
     * @Route("/gestion/villes/add", name="gestion_villes_add")
     * @throws ORMException
     */
    public function add(Request $request, VilleRepository $villeRepository): Response
    {
        $ville = new ville();
        $nomVille = $request->get("nomVille");
        $cp = $request->get("cpVille");

        if (empty($nomVille) or empty($cp)) {
            if (empty(trim($nomVille))) {
                $this->addFlash("error", "Le nom ne peut pas être vide.");
            }
            if (empty(trim($cp))) {
                $this->addFlash("error", "Le code postal ne peut pas être vide.");
            }
        } else {
            $ville->setNom($nomVille)
                ->setCp($cp);
            try {
                $villeRepository->add($ville);
            } catch (OptimisticLockException $e) {
                $this->addFlash("error", "Une erreur est survenue. $e");
            } finally {
                $this->addFlash("success", "Nouvelle ville ajoutée.");
            }

        }
        return $this->redirectToRoute('gestion_villes', []);
    }

    /**
     * @Route("/gestion/villes/{id}/delete", name="gestion_villes_delete", methods={"DELETE"}, requirements={"id": "\d+"})
     * @throws ORMException
     */
    public function delete(Ville $ville, EntityManagerInterface $entityManager): Response
    {
        $nomVille = $ville->getNom();
        try {
            $entityManager->remove($ville);
            $entityManager->flush();
            $entityManager->detach($ville);
        } catch (OptimisticLockException $e) {
            $this->addFlash("error", "Une erreur est survenue. $e");
        }
        $this->addFlash("success", "Ville \"$nomVille\" supprimée.");

        return $this->redirectToRoute('gestion_villes');
    }
}
