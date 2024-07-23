<?php

namespace App\DataFixtures;

use App\Entity\Intervention;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class InterventionFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        // Créer une instance de Faker pour générer des données fictives en français
        // Create a Faker instance to generate fake data in French
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 10; $i++) {
            // Créer une nouvelle instance d'Intervention
            // Create a new instance of Intervention
            $intervention = new Intervention();
            // Générer un tableau de mots aléatoires
            // Generate an array of random words
            $labelArray = $faker->words(3);

            // S'assurer que $labelArray est un tableau
            // Ensure that $labelArray is an array
            if (!is_array($labelArray)) {
                $labelArray = [$labelArray]; // Convertir en tableau si ce n'est pas le cas
                // Convert to array if it's not
            }
            // Joindre les mots avec un espace
            // Join words with space
            $label = join(' ', $labelArray);

            // Définir le label de l'intervention
            // Set the label of the intervention
            $intervention->setLabel($label);
            // Persister l'intervention dans le gestionnaire d'entités
            // Persist the intervention in the entity manager
            $manager->persist($intervention);
            // Ajouter une référence pour l'intervention
            // Add a reference for the intervention
            $this->addReference('intervention-' . $i, $intervention);
        }

        // Flusher les changements dans la base de données
        // Flush the changes to the database
        $manager->flush();
    }

    public static function getGroups(): array
    {
        // Retourner le groupe de fixtures
        // Return the fixture group
        return ['InterventionFixtures'];
    }
}