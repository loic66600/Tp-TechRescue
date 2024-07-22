<?php

namespace App\DataFixtures;

use App\Entity\ContactInformation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class ContactInformationFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 10; $i++) {
            $contact = new ContactInformation();
            $contact->setLastName($faker->lastName);
            $contact->setFirstName($faker->firstName);
            $contact->setPhoneNumber($faker->phoneNumber);
            $manager->persist($contact);
            $this->addReference('contact-' . $i, $contact);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['ContactInformationFixtures'];
    }
}