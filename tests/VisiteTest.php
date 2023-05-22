<?php

namespace App\Tests;

use App\Entity\Exposition;
use App\Entity\Visite;
use PHPUnit\Framework\TestCase;

class VisiteTest extends TestCase
{
    public function testGetNbExpos(): void
    {
        //préparation du contexte de test
        //on créé un objet de la classe Visite
        $visite = new Visite();
        //on créé un objet de la classe Exposition qu'on ajoute à la collection
        //expositions de l'objet $visite
        $expo1 = new Exposition();
        $expo1->setNomExpo('Test1');
        $expo1->setActive(true);
        $expo1->setTarifAdulte(10);
        $expo1->setTarifEnfant(5);
        $visite->addExposition($expo1);

        //on test si le nombre d'expositions attendu est correct
        $nbExpo = $visite->getNbExpos();
        $this->assertEquals(1, $nbExpo, 'Erreur - nb expo incorrect');
    }

    public function testCalculerTarif(): void
    {
        $visite = new Visite();
        //cas visite avec nbAdultes et nbEnfants à null et 0 expo;
        $this->assertEquals(0, $visite->calculerTarif(), 'Erreur avec visiteurs à null, 0 expos');

        //cas visite avec nb adultes et enfants à null et une expo
        $expo1 = new Exposition();
        $expo1->setNomExpo('Test1');
        $expo1->setActive(true);
        $expo1->setTarifAdulte(10);
        $expo1->setTarifEnfant(5);
        $visite->addExposition($expo1);
        $this->assertEquals(0, $visite->calculerTarif(), 'Erreur avec visiteurs à null et 1 expo');

        //cas visite avec nb adultes et enfants à 0 et une expo
        $visite->setNbVisiteursEnfants(0);
        $visite->setNbVisiteursAdultes(0);
        $this->assertEquals(0, $visite->calculerTarif(), 'Erreur avec visiteurs à 0 et 1 expo');

        //cas visite avec nb adultes et enfants != 0 et une expo
        $visite->setNbVisiteursEnfants(2);
        $visite->setNbVisiteursAdultes(1);
        $this->assertEquals(20, $visite->calculerTarif(), 'Erreur avec des visiteurs et 1 expo');

        //cas visite avec nb adultes et enfants != 0 et plusiseurs (2) expos
        $expo2 = new Exposition();
        $expo2->setNomExpo('Test2');
        $expo2->setActive(true);
        $expo2->setTarifAdulte(20);
        $expo2->setTarifEnfant(10);
        $visite->addExposition($expo2);
        $this->assertEquals(60, $visite->calculerTarif(), 'Erreur avec des visiteurs et des expos');
    }
}
