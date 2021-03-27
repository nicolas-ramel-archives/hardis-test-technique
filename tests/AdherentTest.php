<?php
namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AdherentTest extends WebTestCase
{
    /**
     * Exigence : 1
     * Numéro de test : Test 1.1
     * 
     * Description :
     * Etant donné que je passe en paramètre un identifiant connu du fichier CSV
     * Quand j’appelle le WebService
     * Alors le WebService me retourne toutes les informations de mon adhérent
     * 
     * Récupération des infomations de l'adhérent : 1 et contrôle des valeurs
     */
    public function testExigenceTest_1_1() {
        // creation du client web et appel de l'url
        $client = self::createClient();
        $crawler = $client->request('GET', "/adherent/1");

        // test si l'url renvoi une reponse valide
        $this->assertTrue($client->getResponse()->isSuccessful());

        // récupère le contenu de la réponse et decode le JSON retourné
        $adherent = json_decode($client->getResponse()->getContent());

        // test si l'objet contient les bonnes valeurs
        $this->assertEquals(1, $adherent->identifiant);
        $this->assertEquals("Bland", $adherent->nom);
        $this->assertEquals("Angie", $adherent->prenom);
        $this->assertEquals("0611111111", $adherent->telephone);
    }

    /**
     * Exigence : 1
     * Numéro de test : Test 1.2
     * 
     * Description :
     * Etant donné que je passe en paramètre un identifiant inconnu du fichier CSV
     * Quand j’appelle le WebService
     * Alors le WebService me retourne le message «  Aucun adhérent ne correspond à votre demande »
     * 
     * Récupération des infomations de l'adhrent : 9999999999 qui n'existe pas
     * Contrôle le renvoi d'une erreur 404
     * Contrôle du code d'erreur
     */
    public function testExigenceTest_1_2() {
        // creation du client web et appel de l'url
        $client = self::createClient();
        $crawler = $client->request('GET', "/adherent/9999999999");

        // test si la réponse est une erreur
        $this->assertFalse($client->getResponse()->isSuccessful());

        // récupère le contenu de la réponse et decode le JSON retourné
        $reponse = json_decode($client->getResponse()->getContent());

        // test les valeurs de l'erreur retournée
        $this->assertEquals("1.2", $reponse->error_code);
        $this->assertEquals("Aucun adhérent ne correspond à votre demande", $reponse->error);
    }




    /**
     * Exigence : 2
     * Numéro de test : Test 2.1
     * 
     * Description :
     * Etant donné que je ne passe aucun paramètre au WebService
     * Quand j’appelle le WebService
     * Alors le WebService me retourne la liste de tous les adhérents ainsi que leur nombre
     * 
     * Récupération des infomations de l'adhérent : 1 et contrôle des valeurs
     */
    public function testExigenceTest_2_1() {
        // creation du client web et appel de l'url
        $client = self::createClient();
        $crawler = $client->request('GET', "/adherents");

        // test si l'url renvoi une reponse valide
        $this->assertTrue($client->getResponse()->isSuccessful());

        // récupère le contenu de la réponse et decode le JSON retourné
        $reponse = json_decode($client->getResponse()->getContent());

        // test le nombre de résultats retournés
        $this->assertEquals(5, $reponse->nombre_resultats);
    }





    /**
     * Exigence : 2
     * Numéro de test : Test 2.2
     * 
     * Description :
     * Etant donné que je ne passe  aucun paramètre au WebService et que le fichier est vide
     * Quand j’appelle le WebService
     * Alors le WebService me retourne le message « Aucun adhérent n’est présent »
     * 
     * Récupération des infomations de l'adhérent : 1 et contrôle des valeurs
     */
    public function testExigenceTest_2_2() {
        $dossierData = dirname(__DIR__) . "/data/" ;

        // installation du fichier vide dans le dossier data
        rename($dossierData . "adherents.csv", $dossierData . "adherents-tmp.csv");
        rename($dossierData . "adherents-vide.csv", $dossierData . "adherents.csv");

        // creation du client web et appel de l'url
        $client = self::createClient();
        $crawler = $client->request('GET', "/adherents");

        // test si l'url renvoi une reponse valide
        $this->assertTrue($client->getResponse()->isSuccessful());

        // récupère le contenu de la réponse et decode le JSON retourné
        $reponse = json_decode($client->getResponse()->getContent());

        // Test l'erreur retournée car le tableau est vide
        $this->assertEquals("2.2", $reponse->error_code);
        $this->assertEquals("Aucun adhérent n'est présent", $reponse->error);

        // remise en place des bons fichiers dans le dossier data
        rename($dossierData . "adherents.csv", $dossierData . "adherents-vide.csv");
        rename($dossierData . "adherents-tmp.csv", $dossierData . "adherents.csv");
    }





    /**
     * Exigence : 2
     * Numéro de test : Test 2.3
     * 
     * Description :
     * Etant donné que je ne passe  aucun paramètre au WebService et que le fichier n’est pas présent
     * Quand j’appelle le WebService
     * Alors le WebService  me retourne une erreur 404 « Le fichier d’entrée est introuvable »
     * 
     * Récupération des infomations de l'adhérent : 1 et contrôle des valeurs
     */
    public function testExigenceTest_2_3() {
        $dossierData = dirname(__DIR__) . "/data/" ;

        // Renomme temporaire le fichier CSV pour générer une erreur
        rename($dossierData . "adherents.csv", $dossierData . "adherents-tmp.csv");

        // creation du client web et appel de l'url
        $client = self::createClient();
        $crawler = $client->request('GET', "/adherents");

        // test si la réponse est une erreur
        $this->assertFalse($client->getResponse()->isSuccessful());

        // récupère le contenu de la réponse et decode le JSON retourné
        $reponse = json_decode($client->getResponse()->getContent());

        // Test l'erreur retournée car le fichier est introuvable
        $this->assertEquals("2.3", $reponse->error_code);
        $this->assertEquals("Le fichier d'entrée est introuvable", $reponse->error);

        // remise en place du CSV
        rename($dossierData . "adherents-tmp.csv", $dossierData . "adherents.csv");
    }
}