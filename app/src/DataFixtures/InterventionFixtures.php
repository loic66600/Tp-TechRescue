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
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 10; $i++) {
            $intervention = new Intervention();
            $labelArray = $faker->words(3);

            // Ensure that $labelArray is an array
            if (!is_array($labelArray)) {
                $labelArray = [$labelArray]; // Convert to array if it's not
            }
            $label = join(' ', $labelArray); // Join words with space

            $intervention->setLabel($label);
            $manager->persist($intervention);
            $this->addReference('intervention-' . $i, $intervention);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['InterventionFixtures'];
    }
}