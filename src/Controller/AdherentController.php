<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Adherent;


class AdherentController extends AbstractController
{
    /**
     * @Route("/adherent/{idAdherent}", name="getAdherent")
     * 
     * Retourne un adhérent par son ID
     */
    public function getAdherent(Adherent $adherent, int $idAdherent): Response
    {
        // recherche dans le service l'adhérent
        $adherent = $adherent->getAdherent($idAdherent) ;

        // si l'utilisateur est inconnu il faut retourner un message d'erreur (TEST 1.2)
        if (!$adherent) {
            $msgErreur = [];
            $msgErreur["error"] = "Aucun adhérent ne correspond à votre demande" ;
            $msgErreur["error_code"] = "1.2" ;

            // retourne le message d'erreur avec un code HTTP 404
            return $this->json($msgErreur, 404);
        }

        // retourne le json de l'adhérent
        return $this->json($adherent);
    }


    /**
     * @Route("/adherents", name="adherents")
     * 
     * Retourne la liste de tous les adhérents
     */
    public function allAdherents(Adherent $adherent): Response
    {
        // recherche dans le service tous les adhérents
        $adherents = $adherent->allAdherents() ;

        // si le retour est null, il y a eu une erreur lors de la lecture du fichier (TEST 2.3)
        if (!is_array($adherents)) {
            $msgErreur = [];
            $msgErreur["error"] = "Le fichier d'entrée est introuvable" ;
            $msgErreur["error_code"] = "2.3" ;

            // retourne le message d'erreur avec un code HTTP 404
            return $this->json($msgErreur, 404);
        }

        // si le tableau est vide (TEST 2.2)
        if (count($adherents) == 0) {
            $msgErreur = [];
            $msgErreur["error"] = "Aucun adhérent n'est présent" ;
            $msgErreur["error_code"] = "2.2" ;

            // retourne le message d'erreur
            return $this->json($msgErreur);
        }

        // retourne le json du tableau d'adhérents et le nombre de résultats
        $tableauResultats = [];
        $tableauResultats["adherents"] = $adherents ;
        $tableauResultats["nombre_resultats"] = count($adherents) ;
        return $this->json($tableauResultats);
    }
}
