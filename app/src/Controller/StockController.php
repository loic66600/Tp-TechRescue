<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Stock;
use App\Entity\Ticket;
use App\Form\AddStockType;
use App\Entity\InterventionStock;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StockController extends AbstractController
{
    /**
     * Relation ManyToOne avec l'entité User pour le fournisseur
     * ManyToOne relationship with User entity for the supplier
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(name="supplier_id", referencedColumnName="id", nullable=true)
     */
    private $supplier;

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/stock/{ticketId}', name: 'stock_list')]
    public function list(int $ticketId): Response
    {
        // Récupérer le ticket en utilisant l'ID fourni
        // Fetch the ticket using the provided ticket ID
        $ticket = $this->entityManager->getRepository(Ticket::class)->find($ticketId);
        if (!$ticket) {
            // Si le ticket n'existe pas, lancer une exception
            // If the ticket does not exist, throw an exception
            throw $this->createNotFoundException('Le ticket demandé n\'existe pas.');
        }

        // Récupérer le stock disponible
        // Fetch available stock
        $stocks = $this->entityManager->getRepository(Stock::class)->findAll();

        // Rendre la vue avec les stocks et le ticket
        // Render the view with stocks and the ticket
        return $this->render('stock/list.html.twig', [
            'stocks' => $stocks,
            'ticket' => $ticket
        ]);
    }

    #[Route('/stock/use/{id}', name: 'stock_use')]
    public function use(int $id): Response
    {
        // Implémenter la logique d'utilisation du stock ici
        // Implement stock usage logic here

        // Rediriger vers la liste des stocks
        // Redirect to the stock list
        return $this->redirectToRoute('stock_list');
    }

    // Définir le fournisseur
    // Set the supplier
    public function setSupplier(?User $supplier): self
    {
        $this->supplier = $supplier;
        return $this;
    }
}