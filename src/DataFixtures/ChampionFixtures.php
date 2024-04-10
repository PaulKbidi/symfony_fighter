<?php

namespace App\DataFixtures;

use App\Entity\Champion;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ChampionFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $championsList = [
            "Sylas l'Épéiste des Ombres",
            "Aria la Sorcière des Éléments",
            "Drake le Paladin de la Lumière",
            "Lyra la Voleuse Agile",
            "Thalion le Mage Astral",
            "Freya la Gardienne des Anciens",
            "Kael le Chevalier Déchu",
            "Elara la Chasseuse de Monstres",
            "Valeria la Prêtresse Mystique",
            "Ragnar le Guerrier des Royaumes",
            "Lumi la Fée Protectrice",
            "Zephyr le Navigateur des Cieux",
            "Nova la Magicienne Temporelle",
            "Orion le Chasseur d'Étoiles",
            "Seraphina la Maîtresse des Illusions",
            "Gaelle la Druide Enchantée",
            "Asher le Lame de l'Esprit",
            "Selene la Gardienne de la Lune",
            "Tristan le Voyageur Sans Retour",
            "Aurora la Gardienne de l'Aube",
        ];

        foreach ($championsList as $championName) {
            $champion = new Champion();
            $champion
                ->setName($championName)
                ->setPv(mt_rand(500, 800))
                ->setPower(mt_rand(40, 60));
    
            $manager->persist($champion);
        }

        $manager->flush();
    }
}
