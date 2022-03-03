<?php

namespace App\Services;

use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;

class ErrorsService{

    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    //-- gestion des erreurs du formulaire d'une sortie
    public function getSortieErrors($obj, $villeRepo, $lieuRepo){
        
        $errors = [];

        if ($obj["nom-sortie"] == ""){
            $errors[] = "Nom incorrect";
        }

        if ($obj["date-debut"] == ""){
            $errors[] = "Date de début de la sortie incorrecte";
        }

        if ($obj["date-cloture"] == ""){
            $errors[] = "Date limite d'inscription incorrecte";
        }

        if ($obj["nb-places"] == "" || (int) $obj["nb-places"] <= 0){
            $errors[] = "Nombre de places incorrect";
        }

        if ($obj["duree"] == "" || (int) $obj["duree"] <= 0){
            $errors[] = "Durée incorrecte";
        }

        if ($obj["lieu"] == "no"){
            $errors[] = "Lieu incorrect";
        } 
        else if ( $obj["ville"] == "no"){
            $errors[] = "Ville incorrecte";
        }else if ($obj["ville"] != "no" && $obj["lieu"] != "no"){
            if ($lieuRepo->find((int) $obj["lieu"])->getVille() != $villeRepo->find((int) $obj["ville"])){
                $errors[] = "Ville et lieu incompatibles";
            }
        }

        return  $errors;
    }
}