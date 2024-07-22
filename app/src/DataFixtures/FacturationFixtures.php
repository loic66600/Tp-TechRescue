<?php

namespace App\DataFixtures;

use App\Entity\Facturation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class FacturationFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 10; $i++) {
            $facturation = new Facturation();

            // Generate a number with cents
            $value = $faker->randomFloat(2, 10, 500); // Generate a float number between 10 and 500 with 2 decimal places

            $facturation->setValue((string)$value); // Convert value to string
            $manager->persist($facturation);
            $this->addReference('facturation-' . $i, $facturation);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['FacturationFixtures'];
    }
}