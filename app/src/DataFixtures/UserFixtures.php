<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Create an admin user
        $admin = new User();
        $admin->setEmail('admin2@example.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword(
            $admin,
            'admin2'
        ));
        $admin->setActive(true);
        $manager->persist($admin);
        $this->addReference('user-0', $admin);

        // Create a regular user
        $user = new User();
        $user->setEmail('user2@example.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            'user2'
        ));
        $user->setActive(true);
        $manager->persist($user);
        $this->addReference('user-1', $user);

        // Create a first technician user
        $technicien1 = new User();
        $technicien1->setEmail('technicien1@example.com');
        $technicien1->setRoles(['ROLE_TECHNICIEN']);
        $technicien1->setPassword($this->passwordHasher->hashPassword(
            $technicien1,
            'technicien11'
        ));
        $technicien1->setActive(true);
        $manager->persist($technicien1);
        $this->addReference('user-2', $technicien1);

        // Create a second technician user
        $technicien2 = new User();
        $technicien2->setEmail('technicien22@example.com');
        $technicien2->setRoles(['ROLE_TECHNICIEN']);
        $technicien2->setPassword($this->passwordHasher->hashPassword(
            $technicien2,
            'technicien22'
        ));
        $technicien2->setActive(true);
        $manager->persist($technicien2);
        $this->addReference('user-3', $technicien2);

        // Create a first supplier user
        $supplier1 = new User();
        $supplier1->setEmail('supplier11@example.com');
        $supplier1->setRoles(['ROLE_SUPPLIER']);
        $supplier1->setPassword($this->passwordHasher->hashPassword(
            $supplier1,
            'supplier11'
        ));
        $supplier1->setActive(true);
        $manager->persist($supplier1);
        $this->addReference('user-4', $supplier1);

        // Create a second supplier user
        $supplier2 = new User();
        $supplier2->setEmail('supplier22@example.com');
        $supplier2->setRoles(['ROLE_SUPPLIER']);
        $supplier2->setPassword($this->passwordHasher->hashPassword(
            $supplier2,
            'supplier22'
        ));
        $supplier2->setActive(true);
        $manager->persist($supplier2);
        $this->addReference('user-5', $supplier2);

        $manager->flush();
    }
}