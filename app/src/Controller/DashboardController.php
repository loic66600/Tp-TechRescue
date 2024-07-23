<?php

namespace App\Controller;

use App\Entity\ContactInformation;
use App\Entity\Ticket;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        // Récupérer les données de la base de données
        // Fetch data from the database

        // Compter les tickets ouverts
        // Count open tickets
        $openTickets = $this->entityManager->getRepository(Ticket::class)->count(['status' => 'ouvert']);

        // Compter les tickets en cours
        // Count in-progress tickets
        $inProgressTickets = $this->entityManager->getRepository(Ticket::class)->count(['status' => 'en cours']);

        // Compter les tickets résolus aujourd'hui
        // Count tickets resolved today
        $resolvedToday = $this->entityManager->getRepository(Ticket::class)->count(['status' => 'resolus']);

        // Calculer le temps moyen de résolution
        // Calculate average resolution time
        $avgResolutionTime = $this->entityManager->getRepository(Ticket::class)->calculateAverageResolutionTime();

        // Récupérer les tickets récents
        // Fetch recent tickets
        $recentTickets = $this->entityManager->getRepository(Ticket::class)->findRecentTickets();

        // Récupérer tous les tickets
        // Fetch all tickets
        $tickets = $this->entityManager->getRepository(Ticket::class)->findinfos();

        // Récupérer les techniciens
        // Fetch technicians
        $technicians = $this->entityManager->getRepository(User::class)->findByRole('ROLE_TECHNICIEN');
      
        // Combiner les données dans un tableau
        // Combine data into an array
        $data = [
            'openTickets' => $openTickets,
            'inProgressTickets' => $inProgressTickets,
            'resolvedToday' => $resolvedToday,
            'avgResolutionTime' => $avgResolutionTime,
            'recentTickets' => $recentTickets,
            'tickets' => $tickets,
            'technicians' => $technicians,
        ];

        // Rendre la vue
        // Render view
        return $this->render('home/dashboard.html.twig', $data);
    }
}