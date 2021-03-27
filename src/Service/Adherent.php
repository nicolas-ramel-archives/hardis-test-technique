<?php
namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class Adherent {
    private $adherents ;
    private $headerCsvAdherent ;

    /**
     * Constructeur permettant de récupérer le contenu du CSV 
     * et peupler le tableau $adherents par ordre alphabétique nom puis prénom
     */
    public function __construct(ParameterBagInterface $params)
    {
        $this->adherents = null;

        // chemin vers le fichier CSV
        $urlFichierCSV = $params->get('kernel.project_dir') . "/data/adherents.csv";

        // chargement du fichier CSV
        if (is_file($urlFichierCSV)) {
            if (($handle = fopen($urlFichierCSV, "r")) !== FALSE) {
                $this->adherents = [];

                // lecture de toutes les lignes du CSV
                while ($row = fgetcsv($handle, 0, ";")) {
                    if(!$this->headerCsvAdherent) {
                        // récupération de la première ligne pour définir le nom des colonnes
                        $this->headerCsvAdherent= $row;
                    } else {
                        // peuple le tableau des adhérents
                        $this->adherents[] = array_combine($this->headerCsvAdherent, $row);
                    }
                }
                
                // fermeture du fichier CSV
                fclose($handle);
            }

            // tri les adhérents par ordre alphabetique nom + prénom
            usort($this->adherents, function ($a, $b) {
                if ($a["nom"] == $b["nom"] && $a["prenom"] == $b["prenom"] ) {
                    return 0;
                }
                return ($a["nom"] < $b["nom"] || ($a["nom"] == $b["nom"] && $a["prenom"] < $b["prenom"] )) ? -1 : 1;
            });
        }
    }

    /**
     * Retourne un adhérent s'il est présent dans le tableau $adherents
     * sinon renvoi une valeur null
     */
    public function getAdherent(int $idAdherent) {
        if ($this->adherents) {
            // boucle sur le tableau des adhérents pour trouver l'ID
            foreach ($this->adherents as $adherent) {
                if ($adherent["identifiant"] == $idAdherent) {
                    return $adherent ;
                }
            }
        }
        return null ;
    }

    /**
     * Retourne la liste complète des adhérents
     */
    public function allAdherents() {
        return $this->adherents ;
    }
}