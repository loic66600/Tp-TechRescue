<?php

namespace App\DataFixtures;

use App\Entity\Ticket;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class TicketFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        // Créer une instance de Faker pour générer des données fictives en français
        // Create a Faker instance to generate fake data in French
        $faker = Factory::create('fr_FR');

        // Supposons que les techniciens sont user-2 et user-3 selon la configuration précédente de UserFixtures
        // Assuming techniciens are user-2 and user-3 as per previous UserFixtures setup
        $technicienReferences = ['user-2', 'user-3'];

        for ($i = 0; $i < 10; $i++) {
            // Créer une nouvelle instance de Ticket
            // Create a new Ticket instance
            $ticket = new Ticket();

            try {
                // Générer des dates de début et de fin aléatoires
                // Generate random start and end dates
                $dateStart = $faker->dateTimeBetween('now', '+1 month');
                $dateEnd = $faker->dateTimeBetween('+1 month', '+2 month');
                // Générer un statut aléatoire
                // Generate a random status
                $status = $faker->randomElement(['ouvert', 'en cours', 'resolus']);
                // Générer une description aléatoire
                // Generate a random description
                $description = $faker->text(200); // Assurer que le texte est généré en tant que chaîne

                // Assurer une intervention unique pour chaque ticket
                // Ensure unique intervention for each ticket
                $interventionReference = 'intervention-' . $i;

                // Attribuer un technicien aléatoire parmi les références prédéfinies
                // Assign a random technicien from the predefined references
                $technicienReference = $faker->randomElement($technicienReferences);

                // Définir les propriétés du ticket
                // Set the properties of the ticket
                $ticket->setDateStart($dateStart)
                    ->setDateEnd($dateEnd)
                    ->setUser($this->getReference('user-1'))
                    ->setTechnicien($this->getReference($technicienReference))
                    ->setIntervention($this->getReference($interventionReference))
                    ->setStatus($status)
                    ->setDescription($description);

                // Persister le ticket
                // Persist the ticket
                $manager->persist($ticket);

            } catch (\Exception $e) {
                // Gérer les erreurs lors de la création du ticket
                // Handle errors during ticket creation
                echo "Error creating ticket: " . $e->getMessage() . "\n";
            }
        }

        // Flusher les changements dans la base de données
        // Flush the changes to the database
        $manager->flush();
    }

    public function getDependencies(): array
    {
        // Définir les dépendances de cette fixture
        // Define the dependencies for this fixture
        return [
            UserFixtures::class,
            InterventionFixtures::class,
        ];
    }

    public static function getGroups(): array
    {
        // Retourner le groupe de fixtures
        // Return the fixture group
        return ['TicketFixtures'];
    }
}