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
        // Créer une instance de Faker pour générer des données fictives en français
        // Create a Faker instance to generate fake data in French
        $faker = Factory::create('fr_FR');

        // Liste des articles de stock
        // List of stock items
        $stocks = [
            'ordinateur', 'clavier', 'souris',
            'écran', 'câble', 'imprimante',
            'scanner', 'routeur', 'modem',
            'disque dur', 'SSD', 'barrette de RAM',
            'carte mère', 'carte graphique',
            'processeur', 'ventilateur', 'boîtier',
            'alimentation', 'câble HDMI', 'câble USB'
        ];

        // Références des fournisseurs
        // Supplier references
        $suppliers = ['user-4', 'user-5'];

        foreach ($stocks as $s => $stock) {
            // Créer une nouvelle instance de Stock
            // Create a new Stock instance
            $st = new Stock();
            $st->setLabel($stock);
            $st->setReferenceNb('REF-' . strtoupper($stock));
            $st->setQuantity(rand(1, 100));
            $st->setActive(true); // Méthode corrigée / Corrected method name

            try {
                // Attribuer un fournisseur aléatoire à l'article de stock
                // Assign a random supplier to the stock item
                $st->setSupplier($this->getReference($suppliers[rand(0, 1)]));
            } catch (\Exception $e) {
                // Gérer l'erreur si le fournisseur n'est pas trouvé
                // Handle error if supplier is not found
                echo "Error setting supplier for stock item $stock: " . $e->getMessage() . "\n";
                continue;
            }

            // Persister l'article de stock
            // Persist the stock item
            $manager->persist($st);
            // Ajouter une référence pour l'article de stock
            // Add a reference for the stock item
            $this->addReference('stock-' . $s, $st);
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
        ];
    }
}