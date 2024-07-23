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
        $faker = Factory::create('fr_FR');

        // Assuming techniciens are user-2 and user-3 as per previous UserFixtures setup
        $technicienReferences = ['user-2', 'user-3'];

        for ($i = 0; $i < 10; $i++) {
            $ticket = new Ticket();

            try {
                $dateStart = $faker->dateTimeBetween('now', '+1 month');
                $dateEnd = $faker->dateTimeBetween('+1 month', '+2 month');
                $status = $faker->randomElement(['ouvert', 'en cours', 'resolus']);
                $description = $faker->text(200); // Ensuring text is generated as string

                // Ensure unique intervention for each ticket
                $interventionReference = 'intervention-' . $i;

                // Assign a random technicien from the predefined references
                $technicienReference = $faker->randomElement($technicienReferences);

                $ticket->setDateStart($dateStart)
                    ->setDateEnd($dateEnd)
                    ->setUser($this->getReference('user-1'))
                    ->setTechnicien($this->getReference($technicienReference))
                    ->setIntervention($this->getReference($interventionReference))
                    ->setStatus($status)
                    ->setDescription($description);

                $manager->persist($ticket);

            } catch (\Exception $e) {
                echo "Error creating ticket: " . $e->getMessage() . "\n";
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            InterventionFixtures::class,
        ];
    }

    public static function getGroups(): array
    {
        return ['TicketFixtures'];
    }
}