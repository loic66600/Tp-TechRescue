<?php

namespace App\DataFixtures;

use App\Entity\Stock;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class StockFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $stocks = [
            'ordinateur', 'clavier', 'souris',
            'écran', 'câble', 'imprimante',
            'scanner', 'routeur', 'modem',
            'disque dur', 'SSD', 'barrette de RAM',
            'carte mère', 'carte graphique',
            'processeur', 'ventilateur', 'boîtier',
            'alimentation', 'câble HDMI', 'câble USB'
        ];

        $suppliers = ['user-4', 'user-5'];

        foreach ($stocks as $s => $stock) {
            $st = new Stock();
            $st->setLabel($stock);
            $st->setReferenceNb('REF-' . strtoupper($stock));
            $st->setQuantity(rand(1, 100));
            $st->setActive(true); // Corrected method name

            try {
                $st->setSupplier($this->getReference($suppliers[rand(0, 1)]));
            } catch (\Exception $e) {
                echo "Error setting supplier for stock item $stock: " . $e->getMessage() . "\n";
                continue;
            }

            $manager->persist($st);
            $this->addReference('stock-' . $s, $st);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}